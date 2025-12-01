public function up()
{
    Schema::create('demo_logs', function (Blueprint $table) {
        $table->id();
        $table->string('ip')->nullable();
        $table->string('user_agent')->nullable();
        $table->timestamp('visited_at')->useCurrent();
    });
}