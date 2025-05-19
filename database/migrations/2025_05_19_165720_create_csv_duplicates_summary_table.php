<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsvDuplicatesSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('csv_duplicates_summary', function(Blueprint $table) {
                $table->unsignedBigInteger('csv_uploads_id');
                $table->unsignedBigInteger('csv_id');
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('total_errors');
                $table->primary(['csv_uploads_id', 'csv_id']);
                $table->index(['csv_uploads_id', 'csv_id']);
                $table->index('start_date');
                $table->index('end_date');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csv_duplicates_summary');

    }
}
