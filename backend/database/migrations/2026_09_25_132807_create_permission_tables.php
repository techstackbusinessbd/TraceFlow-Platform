<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Spatie Permission Tables with UUID Primary Keys & Multi-Company Teams (ADR-08, Invariant-01).
     * team_foreign_key is set to `company_id` (UUID v7 referencing `companies` table).
     * model_morph_key is set to `model_id` (UUID v7 referencing `users` table).
     */
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        // 1. Permissions Table (UUID PK)
        Schema::create($tableNames['permissions'], static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 125);
            $table->string('guard_name', 50);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // 2. Roles Table (UUID PK with optional company_id for Multi-Company isolation)
        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames): void {
            $table->uuid('id')->primary();
            if ($teams || config('permission.testing')) {
                $table->foreignUuid($columnNames['team_foreign_key'])
                    ->nullable()
                    ->constrained('companies')
                    ->cascadeOnDelete();
                $table->index($columnNames['team_foreign_key'], 'roles_company_id_index');
            }
            $table->string('name', 125);
            $table->string('guard_name', 50);
            $table->timestamps();

            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name'], 'roles_company_name_guard_unique');
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        // 3. Model Has Permissions (Pivot with UUID model_id and UUID company_id)
        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams): void {
            $table->foreignUuid($pivotPermission)
                ->constrained($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->string('model_type');
            $table->uuid($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            if ($teams) {
                $table->foreignUuid($columnNames['team_foreign_key'])
                    ->constrained('companies')
                    ->cascadeOnDelete();
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_company_id_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_primary'
                );
            } else {
                $table->primary(
                    [$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_primary'
                );
            }
        });

        // 4. Model Has Roles (Pivot with UUID model_id and UUID company_id)
        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams): void {
            $table->foreignUuid($pivotRole)
                ->constrained($tableNames['roles'])
                ->cascadeOnDelete();

            $table->string('model_type');
            $table->uuid($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            if ($teams) {
                $table->foreignUuid($columnNames['team_foreign_key'])
                    ->constrained('companies')
                    ->cascadeOnDelete();
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_company_id_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_primary'
                );
            } else {
                $table->primary(
                    [$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_primary'
                );
            }
        });

        // 5. Role Has Permissions (UUID pivots)
        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission): void {
            $table->foreignUuid($pivotPermission)
                ->constrained($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->foreignUuid($pivotRole)
                ->constrained($tableNames['roles'])
                ->cascadeOnDelete();

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        throw_if(empty($tableNames), 'Error: config/permission.php not found and defaults could not be merged.');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
