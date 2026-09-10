<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Passkeys\Passkeys;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('passkeys', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedInteger('user_id'); 
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            $table->string('credential_id')->unique();
            $table->text('credential'); 
            $table->string('name');
            
            // DITO ANG PAGBABAGO: Idinagdag natin ang last_used_at
            $table->timestamp('last_used_at')->nullable(); 
            
            $table->timestamps();
        });
    }
};
