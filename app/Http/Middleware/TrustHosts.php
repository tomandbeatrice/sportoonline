public function hosts()
{
    return [
        $this->allSubdomainsOfApplicationUrl(),
        'localhost',
        '127.0.0.1',
        'test.sportoonline.com',
        'cdn.test.sportoonline.com',
    ];
}