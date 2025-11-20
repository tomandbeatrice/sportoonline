// database/migrations/xxxx_create_variants_table.php
public function up()
{
    Schema::create('variants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->string('color')->nullable();
        $table->string('size')->nullable();
        $table->decimal('variant_price', 10, 2)->nullable();
        $table->timestamps();
    });
}