$host = $this->option('host') ?? '127.0.0.1';
$port = $this->option('port') ?? '8000';
$publicPath = base_path('public');

$command = sprintf(
    '"%s" -S %s:%s -t "%s"',
    PHP_BINARY,
    $host,
    $port,
    $publicPath
);

$process = new Process($command);
$process->start();

foreach ($process as $line) {
    $this->output->writeln($line);

    // Hata toleranslı tarih parse
    $regex = '/^\[([^\]]+)\]/';
    if (preg_match($regex, $line, $matches) && isset($matches[1])) {
        try {
            $timestamp = Carbon::createFromFormat('D M d H:i:s Y', $matches[1]);
            // Log zamanını kullanmak istiyorsan burada işlem yapabilirsin
        } catch (\Exception $e) {
            // Tarih formatı uyumsuzsa logla ama hata fırlatma
            $this->output->writeln("Log satırı parse edilemedi: " . $e->getMessage());
        }
    }
}