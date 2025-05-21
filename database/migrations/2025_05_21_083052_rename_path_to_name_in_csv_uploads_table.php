<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

    class RenamePathToNameInCsvUploadsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('csv_uploads', function(Blueprint $table) {
                $table->renameColumn('path', 'name');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('csv_uploads', function(Blueprint $table) {
                $table->renameColumn('name', 'path');
            });
        }
    }
