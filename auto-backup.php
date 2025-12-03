<?php
  // Settings
  $cpaneluser      = 'cpanelusername';      // cPanel username
  $cpaneluserpass  = 'cpanelpassword';      // cPanel password
  $theme           = 'paper_lantern';       // cPanel theme ('paper_lantern' is most common)
  $ftp             = true;                  // If false, backup will be stored in user's home directory, else uploaded via FTP
  $ftpserver       = 'ftp.domain.com';      // FTP server hostname (use 'localhost' for local server)
  $ftpusername     = 'ftpusername';         // FTP username (use cPanel username for local, custom for remote)
  $ftppassword     = 'ftppassword';         // FTP password (same as cPanel password for local, custom for remote)
  $ftpport         = 21;                    // FTP port (typically 21 for FTP)
  $ftpdirectory    = '/backup';             // Directory on FTP server to store backups. MUST EXIST BEFORE BACKUP.

  // Do not edit below this line
  $domain          = 'localhost';           // Domain of cPanel server
  $secure          = true;                  // If true, use SSL connection, otherwise use non-secure connection
  $auth            = base64_encode($cpaneluser . ":" . $cpaneluserpass);  // Authentication string for cPanel
  $url             = $secure ? "ssl://$domain" : $domain;  // Set protocol to ssl if secure
  $port            = $secure ? 2083 : 2082;  // Set port for SSL (2083) or non-SSL (2082)

  // Open socket connection to cPanel server
  $socket = fsockopen($domain, $port);
  if (!$socket) {  
      exit("Failed to open socket connection.");
  }

  // Set backup parameters
  if ($ftp) {
      $params = "dest=ftp&server=$ftpserver&user=$ftpusername&pass=$ftppassword&port=$ftpport&rdir=$ftpdirectory&submit=Generate Backup";
  } else {
      $params = "submit=Generate Backup";
  }

  // Send HTTP request to initiate backup
  fputs($socket, "POST /frontend/$theme/backup/dofullbackup.html?$params HTTP/1.0\r\n");
  fputs($socket, "Host: $domain\r\n");
  fputs($socket, "Authorization: Basic $auth\r\n");
  fputs($socket, "Connection: Close\r\n");
  fputs($socket, "\r\n");

  // Read and ignore the response (optional for debugging)
  while (!feof($socket)) {
      $response = fgets($socket, 4096);
      // Uncomment to debug response
      // echo $response;
  }

  // Close the socket connection
  fclose($socket);
?>
