
<?php

class HAProxyRuntime
{
    private string $socketPath = '/run/haproxy/admin.sock';
    private string $backend = 'web_backend';

    //Envoie une commande a HAProxy via son socket Unix.
    private function execute(string $command): string
    {
        $socket = @stream_socket_client(
            'unix://' . $this->socketPath,
            $errno,
            $errstr,
            3
        );

        if (!$socket) {
            throw new RuntimeException(
                "Impossible de communiquer avec HAProxy : $errstr ($errno)"
            );
        }

        stream_set_timeout($socket, 3);

        fwrite($socket, $command . "\n");

        $response = '';

        while (!feof($socket)) {
            $chunk = fread($socket, 8192);

            if ($chunk === false || $chunk === '') {
                break;
            }

            $response .= $chunk;
        }

        fclose($socket);

        return trim($response);
    }

    //Recupere les statistiques CSV de HAProxy.

    public function getStats(): array
    {
        $response = $this->execute('show stat');

        if ($response === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $response);
        $headerLine = array_shift($lines);

        // Supprime le # au début de la ligne des en-têtes
        $headerLine = ltrim($headerLine, '# ');

        $header = str_getcsv($headerLine);

        $stats = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $values = str_getcsv($line);

            if (count($values) !== count($header)) {
                continue;
            }

            $row = array_combine($header, $values);

            if ($row !== false) {
                $stats[] = $row;
            }
        }

        return $stats;
    }

    //Retourne uniquement les serveurs de notre backend.
    
    public function getServers(): array
    {
        $stats = $this->getStats();
        $servers = [];

        foreach ($stats as $row) {
            if (
                ($row['pxname'] ?? '') !== $this->backend ||
                in_array($row['svname'] ?? '', ['BACKEND', 'FRONTEND'])
            ) {
                continue;
            }

            $status = strtoupper($row['status'] ?? 'UNKNOWN');

            $servers[] = [
                'id' => $row['svname'],
                'name' => $row['svname'],
                'address' => $row['addr'] ?? '',
                'status' => $status,
                'requests' => (int) ($row['lbtot'] ?? 0),
                'sessions' => (int) ($row['scur'] ?? 0),
                'disabled' => in_array($status, ['MAINT', 'MAINTENANCE']),
            ];
        }

        return $servers;
    }

    /**
     * Retourne l'etat general du backend.
     */
    public function getDashboard(): array
    {
        $servers = $this->getServers();

        $configured = count($servers);
        $available = 0;
        $unavailable = 0;

        foreach ($servers as $server) {
            if ($server['status'] === 'UP') {
                $available++;
            } else {
                $unavailable++;
            }
        }

        return [
            'haproxy' => $this->getServiceStatus(),
            'backend' => $this->backend,
            'configured' => $configured,
            'available' => $available,
            'unavailable' => $unavailable,
            'servers' => $servers,
        ];
    }

    /**
     * Verifie si le processus HAProxy est joignable via le socket.
     */
    public function getServiceStatus(): string
    {
        try {
            $this->execute('show info');
            return 'ACTIF';
        } catch (Throwable $e) {
            return 'INACTIF';
        }
    }

    /**
     * Desactive un serveur.
     */
    public function disableServer(string $serverId): bool
    {
        $this->validateServer($serverId);

        $response = $this->execute(
            'disable server ' . $this->backend . '/' . $serverId
        );

        return $response === '' || stripos($response, 'error') === false;
    }

    /**
     * Reactive un serveur.
     */
    public function enableServer(string $serverId): bool
    {
        $this->validateServer($serverId);

        $response = $this->execute(
            'enable server ' . $this->backend . '/' . $serverId
        );

        return $response === '' || stripos($response, 'error') === false;
    }

    /**
     * Limite les actions aux serveurs autorises.
     */
    private function validateServer(string $serverId): void
    {
        $allowed = ['PC1', 'PC2'];

        if (!in_array($serverId, $allowed, true)) {
            throw new InvalidArgumentException(
                'Serveur non autorise : ' . $serverId
            );
        }
    }
}
