<?php

use App\Enums\MailTypeEnum;
use App\Enums\MailStatusEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('mail_type', MailTypeEnum::toArray())->nullable();
            $table->string('sender_name');
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->text('description');
            $table->enum('mail_status',  MailStatusEnum::toArray())->default(MailStatusEnum::Pending->value);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->enum('payment_status', PaymentStatusEnum::toArray())->default(PaymentStatusEnum::Pending->value);
            $table->dateTime('recieved_at')->nullable();
            $table->dateTime('forwarded_at')->nullable();
            $table->dateTime('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
