<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateCsvRecordsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('csv_records', function(Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('csv_id')->index();
                $table->unsignedBigInteger('csv_uploads_id')->index();
                $table->string('zona', 1)->index();
                $table->date('fecha_desde')->index();
                $table->date('fecha_hasta')->index();
                $table->timestamps();
                $table->index(['csv_id','fecha_desde', 'fecha_hasta']);
                $table->foreign('csv_uploads_id')->references('id')->on('csv_uploads')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('csv_records');
        }
    }
