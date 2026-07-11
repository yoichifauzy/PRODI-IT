<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ==========================================
        // 3. TABEL DATA AKADEMIK & SINKRONISASI
        // ==========================================
        Schema::create('asset_links', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'kurikulum', 'penelitian-pengabdian', dll
            $table->text('url');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // ==========================================
        // 5. TABEL PROFILING & UI ASSETS
        // ==========================================
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->text('description_primary')->nullable();
            $table->text('description_secondary')->nullable();
            $table->text('vision_text')->nullable();
            $table->text('mission_text')->nullable();
            $table->string('image_one_path')->nullable();
            $table->string('image_two_path')->nullable();
            $table->string('video_path')->nullable();
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('image_path');
            $table->integer('position')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('image_path');
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('vision_missions', function (Blueprint $table) {
            $table->id();
            $table->string('vision_title')->default('Visi');
            $table->text('vision_text');
            $table->string('mission_title')->default('Misi');
            $table->longText('mission_text');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        // ==========================================
        // 2. TABEL KONTEN PUBLIKASI & KEGIATAN
        // ==========================================
        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // UAS, UTS, Lainnya
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('google_calendar_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('category', 120);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->date('event_date');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('image_path')->nullable();
            $table->string('google_event_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('student_name');
            $table->string('student_nim', 30)->nullable();
            $table->smallInteger('year')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_feature')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->integer('year');
            $table->string('image_path');
            $table->unsignedBigInteger('position')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // ==========================================
        // 3. TABEL DATA AKADEMIK (Lanjutan)
        // ==========================================
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->string('semester', 50);
            $table->string('major_selection', 100)->nullable();
            $table->string('code', 50);
            $table->string('name');
            $table->integer('credits_theory');
            $table->integer('credits_practice');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('asset_links')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('researches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->string('title');
            $table->string('researcher_name')->nullable();
            $table->smallInteger('year')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('asset_links')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('community_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->string('title');
            $table->string('location')->nullable();
            $table->smallInteger('year')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('asset_links')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('tracer_alumnis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->string('nim', 30)->unique();
            $table->string('name')->nullable();
            $table->smallInteger('graduation_year')->nullable();
            $table->string('company_name');
            $table->string('department')->nullable();
            $table->string('relevance')->nullable();
            $table->string('contact', 30)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('asset_links')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('learning_outcome', function (Blueprint $table) {
            $table->id();
            $table->string('code')->index();
            $table->text('description');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // ==========================================
        // 4. TABEL SDM & BLOG
        // ==========================================
        Schema::create('lecturer_staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->enum('type', ['lecturer', 'staff'])->default('lecturer');
            $table->string('email')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('lecturer_staff_blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lecturer_staff_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->date('activity_date')->nullable();
            $table->string('cover_image')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->foreign('lecturer_staff_id')->references('id')->on('lecturer_staff')->onDelete('cascade');
        });

        // ==========================================
        // 6. TABEL INTERAKSI PENGGUNA
        // ==========================================
        Schema::create('aspirations', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('nim', 30)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['unread', 'read', 'archived'])->default('unread');
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('read_by')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('read_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspirations');
        Schema::dropIfExists('lecturer_staff_blogs');
        Schema::dropIfExists('lecturer_staff');
        
        Schema::dropIfExists('learning_outcome');
        Schema::dropIfExists('tracer_alumnis');
        Schema::dropIfExists('community_services');
        Schema::dropIfExists('researches');
        Schema::dropIfExists('courses');

        Schema::dropIfExists('galleries');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('academic_calendars');

        Schema::dropIfExists('vision_missions');
        Schema::dropIfExists('hero_slides');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('profiles');
        
        Schema::dropIfExists('asset_links');
    }
};
