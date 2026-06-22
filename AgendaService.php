<?php
/**
 * AgendaService.php
 * Salvar em: services/AgendaService.php  (ou na raiz, onde estava originalmente)
 */

require_once __DIR__ . '/vendor/autoload.php';

class AgendaService
{
    private $client;

    public function __construct()
    {
        $this->client = new Google\Client();
        $this->client->setAuthConfig(__DIR__ . '/credentials.json');
        $this->client->addScope(Google\Service\Calendar::CALENDAR);
        $this->client->setRedirectUri('http://localhost/gestiq/callback_google.php');
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
    }

    // -------------------------------------------------------------------------
    // Autenticação OAuth
    // -------------------------------------------------------------------------

    /**
     * Gera a URL do Google para onde o usuário deve ser redirecionado para login.
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Recebe o código do Google e troca pelo Token de Acesso.
     */
    public function autenticarComCodigo(string $codigo): array
    {
        return $this->client->fetchAccessTokenWithAuthCode($codigo);
    }

    // -------------------------------------------------------------------------
    // Método interno: busca token no banco e renova se expirado
    // -------------------------------------------------------------------------

    /**
     * Autentica o cliente Google silenciosamente usando o token salvo no banco.
     * Renova o access_token automaticamente via refresh_token quando necessário.
     *
     * @throws Exception se o usuário ainda não autorizou o Google Agenda.
     */
    private function getClientAutenticado(int $usuario_id): Google\Client
    {
        $pdo = new PDO("mysql:host=localhost;dbname=gestiq;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare(
            "SELECT access_token, refresh_token FROM google_tokens WHERE usuario_id = :id"
        );
        $stmt->execute([':id' => $usuario_id]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tokenData) {
            throw new Exception("O usuário ainda não autorizou o Google Agenda.");
        }

        $this->client->setAccessToken([
            'access_token'  => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'],
        ]);

        // Se o access_token de 1 hora expirou, renova automaticamente via refresh_token
        if ($this->client->isAccessTokenExpired()) {
            $novoToken = $this->client->fetchAccessTokenWithRefreshToken(
                $this->client->getRefreshToken()
            );

            $stmtUp = $pdo->prepare(
                "UPDATE google_tokens SET access_token = :ac WHERE usuario_id = :id"
            );
            $stmtUp->execute([
                ':ac' => $novoToken['access_token'],
                ':id' => $usuario_id,
            ]);

            $this->client->setAccessToken($novoToken);
        }

        return $this->client;
    }

    // -------------------------------------------------------------------------
    // Eventos
    // -------------------------------------------------------------------------

    /**
     * Cria um evento na agenda principal do usuário.
     *
     * @param  int    $usuario_id
     * @param  string $titulo
     * @param  string $descricao
     * @param  string $dataHoraInicio  ISO 8601 — ex.: 2025-08-10T14:00:00-03:00
     * @param  string $dataHoraFim     ISO 8601 — ex.: 2025-08-10T15:00:00-03:00
     * @return string URL do evento criado
     */
    /**
     * Cria o evento na agenda e envia e-mail para os usuários convidados
     */
    public function criarEvento($usuario_id, $titulo, $descricao, $dataHoraInicio, $dataHoraFim, $emailsConvidados = []) {
        $client = $this->getClientAutenticado($usuario_id);
        $service = new Google\Service\Calendar($client);

        // 1. Prepara a lista de e-mails no formato que o Google exige
        $attendees = [];
        if (!empty($emailsConvidados)) {
            foreach ($emailsConvidados as $email) {
                if (!empty($email)) {
                    $attendees[] = ['email' => $email];
                }
            }
        }

        // 2. Monta o corpo do evento
        $eventoConfig = [
            'summary' => $titulo,
            'description' => $descricao,
            'start' => [
                'dateTime' => $dataHoraInicio,
                'timeZone' => 'America/Sao_Paulo',
            ],
            'end' => [
                'dateTime' => $dataHoraFim,
                'timeZone' => 'America/Sao_Paulo',
            ],
        ];

        // 3. Se houver participantes selecionados, anexa ao evento
        if (!empty($attendees)) {
            $eventoConfig['attendees'] = $attendees;
        }

        $evento = new Google\Service\Calendar\Event($eventoConfig);

        // 4. MÁGICA: 'sendUpdates' => 'all' força o Google a enviar e-mails de convite
        $eventoCriado = $service->events->insert('primary', $evento, ['sendUpdates' => 'all']);
        
        return $eventoCriado->htmlLink;
    }
    /**
     * Lista eventos dentro de um intervalo de datas.
     * Usado pelo AgendaController::dashboard() para exibir os próximos 30 dias.
     *
     * @param  int    $usuario_id
     * @param  string $timeMin  ISO 8601 (início do intervalo)
     * @param  string $timeMax  ISO 8601 (fim do intervalo)
     * @param  int    $maxResults
     * @return array  Array de Google\Service\Calendar\Event
     */
    public function listarEventos(
        int $usuario_id,
        string $timeMin,
        string $timeMax,
        int $maxResults = 20
    ): array {
        $client  = $this->getClientAutenticado($usuario_id); // corrigido: era getClient()
        $service = new Google\Service\Calendar($client);

        $resultado = $service->events->listEvents('primary', [
            'timeMin'      => $timeMin,
            'timeMax'      => $timeMax,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'maxResults'   => $maxResults,
        ]);

        return $resultado->getItems();
    }

    /**
     * Atalho: lista os próximos N eventos a partir de agora.
     * Mantido por compatibilidade — internamente usa listarEventos().
     *
     * @param  int $usuario_id
     * @param  int $quantidade
     * @return array
     */
    public function listarProximosEventos(int $usuario_id, int $quantidade = 5): array
    {
        $agora  = (new DateTime())->format(DateTime::ATOM);
        $limite = (new DateTime('+1 year'))->format(DateTime::ATOM);

        return $this->listarEventos($usuario_id, $agora, $limite, $quantidade);
    }
}