Schema::create('invitation_codes', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->unsignedBigInteger('inviter_id');
    $table->unsignedBigInteger('used_by_id')->nullable();
    $table->timestamp('used_at')->nullable();
    $table->timestamps();
});