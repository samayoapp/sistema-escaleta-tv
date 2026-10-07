<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Show;
use App\Models\Rundown;
use App\Models\ProductionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RundownDateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure standard production types
        ProductionType::firstOrCreate(
            ['value' => 'live'],
            [
                'label'        => 'Programa en Vivo',
                'has_air_time' => true,
                'has_lock'     => true,
                'has_episode'  => false,
                'active'       => true,
            ]
        );

        ProductionType::firstOrCreate(
            ['value' => 'reality'],
            [
                'label'        => 'Reality de TV',
                'has_air_time' => false,
                'has_lock'     => false,
                'has_episode'  => true,
                'active'       => true,
            ]
        );

        ProductionType::firstOrCreate(
            ['value' => 'documental'],
            [
                'label'        => 'Documental',
                'has_air_time' => false,
                'has_lock'     => false,
                'has_episode'  => true,
                'active'       => true,
            ]
        );
    }

    public function test_can_create_rundown_with_delivery_date_and_air_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $show = Show::create([
            'title'           => 'Documental Historia',
            'production_type' => 'documental',
            'status'          => 'active',
        ]);

        $response = $this->actingAs($admin)->post("/shows/{$show->id}/rundowns", [
            'air_date'       => '2026-11-20',
            'delivery_date'  => '2026-11-15',
            'episode_number' => 1,
            'episode_name'   => 'Episodio Piloto',
        ]);

        $this->assertDatabaseHas('rundowns', [
            'show_id'        => $show->id,
            'air_date'       => '2026-11-20',
            'delivery_date'  => '2026-11-15',
            'episode_number' => 1,
            'episode_name'   => 'Episodio Piloto',
        ]);

        $rundown = Rundown::where('show_id', $show->id)->first();
        $response->assertRedirect("/rundown/{$rundown->id}");
    }

    public function test_can_update_rundown_delivery_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $show = Show::create([
            'title'           => 'Documental Historia',
            'production_type' => 'documental',
            'status'          => 'active',
        ]);

        $rundown = Rundown::create([
            'show_id'        => $show->id,
            'air_date'       => '2026-11-20',
            'delivery_date'  => '2026-11-15',
            'episode_number' => 1,
            'episode_name'   => 'Episodio Piloto',
            'status'         => 'borrador',
        ]);

        $this->actingAs($admin)->post("/rundown/{$rundown->id}/update-datetime", [
            'air_date'       => '2026-11-25',
            'delivery_date'  => '2026-11-18',
            'episode_number' => 2,
            'episode_name'   => 'Nuevo Nombre',
        ]);

        $rundown->refresh();
        $this->assertEquals('2026-11-25', $rundown->air_date->format('Y-m-d'));
        $this->assertEquals('2026-11-18', $rundown->delivery_date->format('Y-m-d'));
        $this->assertEquals(2, $rundown->episode_number);
    }

    public function test_live_show_locks_when_air_date_is_past(): void
    {
        $show = Show::create([
            'title'           => 'Noticiero en Vivo',
            'production_type' => 'live',
            'status'          => 'active',
        ]);

        $pastRundown = Rundown::create([
            'show_id'  => $show->id,
            'air_date' => '2026-01-01',
            'air_time' => '12:00:00',
            'status'   => 'borrador',
        ]);

        $this->assertTrue($pastRundown->isLocked());
    }

    public function test_documental_without_lock_flag_does_not_lock_in_past(): void
    {
        $show = Show::create([
            'title'           => 'Docu No Lock',
            'production_type' => 'documental',
            'status'          => 'active',
        ]);

        $pastRundown = Rundown::create([
            'show_id'       => $show->id,
            'air_date'      => '2026-01-01',
            'delivery_date' => '2025-12-20',
            'status'        => 'borrador',
        ]);

        $this->assertFalse($pastRundown->isLocked());
    }

    public function test_documental_with_lock_flag_locks_when_air_date_is_past(): void
    {
        $pt = ProductionType::where('value', 'documental')->first();
        $pt->update(['has_lock' => true]);
        ProductionType::clearCache();

        $show = Show::create([
            'title'           => 'Docu With Lock',
            'production_type' => 'documental',
            'status'          => 'active',
        ]);

        $pastRundown = Rundown::create([
            'show_id'       => $show->id,
            'air_date'      => '2026-01-01',
            'delivery_date' => '2025-12-20',
            'status'        => 'borrador',
        ]);

        $this->assertTrue($pastRundown->isLocked());
    }

    public function test_duplicate_rundown_keeps_delivery_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $show = Show::create([
            'title'           => 'Reality Show',
            'production_type' => 'reality',
            'status'          => 'active',
        ]);

        $rundown = Rundown::create([
            'show_id'        => $show->id,
            'air_date'       => '2026-10-10',
            'delivery_date'  => '2026-10-05',
            'episode_number' => 1,
            'episode_name'   => 'Capítulo 1',
            'status'         => 'borrador',
        ]);

        $response = $this->actingAs($admin)->post("/rundown/{$rundown->id}/duplicate", [
            'air_date'       => '2026-10-17',
            'delivery_date'  => '2026-10-12',
            'episode_number' => 2,
            'episode_name'   => 'Capítulo 2',
        ]);

        $this->assertDatabaseHas('rundowns', [
            'show_id'        => $show->id,
            'air_date'       => '2026-10-17',
            'delivery_date'  => '2026-10-12',
            'episode_number' => 2,
            'episode_name'   => 'Capítulo 2',
        ]);
    }

    public function test_rundown_view_displays_episode_title_and_dates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $show = Show::create([
            'title'           => 'Expedición Selva',
            'production_type' => 'documental',
            'status'          => 'active',
        ]);

        $rundown = Rundown::create([
            'show_id'        => $show->id,
            'air_date'       => '2026-11-20',
            'delivery_date'  => '2026-11-15',
            'episode_number' => 5,
            'episode_name'   => 'El Gran Felino',
            'status'         => 'borrador',
        ]);

        $response = $this->actingAs($admin)->get("/rundown/{$rundown->id}");

        $response->assertStatus(200);
        $response->assertSee('EP 5');
        $response->assertSee('El Gran Felino');
        $response->assertSee('Fecha de Aire:');
        $response->assertSee('20/11/2026');
        $response->assertSee('Fecha de Entrega:');
        $response->assertSee('15/11/2026');
    }

    public function test_pdf_escaleta_and_guion_generate_with_episode_and_dates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $show = Show::create([
            'title'           => 'Expedición Selva',
            'production_type' => 'documental',
            'status'          => 'active',
        ]);

        $rundown = Rundown::create([
            'show_id'        => $show->id,
            'air_date'       => '2026-11-20',
            'delivery_date'  => '2026-11-15',
            'episode_number' => 5,
            'episode_name'   => 'El Gran Felino',
            'status'         => 'borrador',
        ]);

        // Escaleta PDF
        $resEscaleta = $this->actingAs($admin)->get("/rundown/{$rundown->id}/pdf-escaleta");
        $resEscaleta->assertStatus(200);
        $resEscaleta->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('escaleta-expedicion-selva-ep5-el-gran-felino-2026-11-20.pdf', $resEscaleta->headers->get('content-disposition'));

        // Guion PDF
        $resGuion = $this->actingAs($admin)->get("/rundown/{$rundown->id}/pdf");
        $resGuion->assertStatus(200);
        $resGuion->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('guion-expedicion-selva-ep5-el-gran-felino-2026-11-20.pdf', $resGuion->headers->get('content-disposition'));

        // Blade view contents directly
        $viewEscaleta = view('pdf.escaleta', compact('rundown'))->render();
        $this->assertStringContainsString('EP 5', $viewEscaleta);
        $this->assertStringContainsString('El Gran Felino', $viewEscaleta);
        $this->assertStringContainsString('20/11/2026', $viewEscaleta);
        $this->assertStringContainsString('15/11/2026', $viewEscaleta);

        $viewGuion = view('pdf.guion', compact('rundown'))->render();
        $this->assertStringContainsString('EP 5', $viewGuion);
        $this->assertStringContainsString('El Gran Felino', $viewGuion);
        $this->assertStringContainsString('20/11/2026', $viewGuion);
        $this->assertStringContainsString('15/11/2026', $viewGuion);
    }

    public function test_teleprompter_displays_episode_and_dates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $show = Show::create([
            'title'           => 'Expedición Selva',
            'production_type' => 'documental',
            'status'          => 'active',
        ]);

        $rundown = Rundown::create([
            'show_id'        => $show->id,
            'air_date'       => '2026-11-20',
            'delivery_date'  => '2026-11-15',
            'episode_number' => 5,
            'episode_name'   => 'El Gran Felino',
            'status'         => 'borrador',
        ]);

        $response = $this->actingAs($admin)->get("/rundown/{$rundown->id}/prompter");
        $response->assertStatus(200);
        $response->assertSee('EP 5');
        $response->assertSee('El Gran Felino');
        $response->assertSee('20/11/2026');
        $response->assertSee('15/11/2026');
    }

    public function test_live_show_hybrid_model_default_by_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $show = Show::create([
            'title'           => 'Noticiero Estelar',
            'production_type' => 'live',
            'status'          => 'active',
        ]);

        $rundown = Rundown::create([
            'show_id'  => $show->id,
            'air_date' => '2026-10-07', // Miércoles
            'air_time' => '19:00:00',
            'status'   => 'borrador',
        ]);

        $this->assertTrue($rundown->isLive());
        $this->assertStringContainsString('Edición', $rundown->getEditionTitle());
        $this->assertStringContainsString('07/10/2026', $rundown->getEditionTitle());

        // Rundown view
        $response = $this->actingAs($admin)->get("/rundown/{$rundown->id}");
        $response->assertStatus(200);
        $response->assertSee('EN VIVO');
        $response->assertSee($rundown->getEditionTitle());

        // Teleprompter
        $resPrompter = $this->actingAs($admin)->get("/rundown/{$rundown->id}/prompter");
        $resPrompter->assertStatus(200);
        $resPrompter->assertSee('EN VIVO');
        $resPrompter->assertSee($rundown->getEditionTitle());

        // Escaleta view
        $viewEscaleta = view('pdf.escaleta', compact('rundown'))->render();
        $this->assertStringContainsString('EN VIVO', $viewEscaleta);
        $this->assertStringContainsString($rundown->getEditionTitle(), $viewEscaleta);

        // Guion view
        $viewGuion = view('pdf.guion', compact('rundown'))->render();
        $this->assertStringContainsString('EN VIVO', $viewGuion);
        $this->assertStringContainsString($rundown->getEditionTitle(), $viewGuion);
    }

    public function test_live_show_hybrid_model_custom_edition_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $show = Show::create([
            'title'           => 'Noticiero Estelar',
            'production_type' => 'live',
            'status'          => 'active',
        ]);

        $response = $this->actingAs($admin)->post("/shows/{$show->id}/rundowns", [
            'air_date'     => '2026-10-07',
            'air_time'     => '12:00:00',
            'episode_name' => 'Edición Mediodía',
        ]);

        $rundown = Rundown::where('show_id', $show->id)->first();
        $this->assertEquals('Edición Mediodía', $rundown->getEditionTitle());
        $this->assertEquals('Edición Mediodía', $rundown->episode_name);
        $this->assertNull($rundown->episode_number);

        // Escaleta PDF
        $resEscaleta = $this->actingAs($admin)->get("/rundown/{$rundown->id}/pdf-escaleta");
        $resEscaleta->assertStatus(200);
        $this->assertStringContainsString('escaleta-noticiero-estelar-edicion-mediodia-2026-10-07.pdf', $resEscaleta->headers->get('content-disposition'));

        // Rundown view
        $resView = $this->actingAs($admin)->get("/rundown/{$rundown->id}");
        $resView->assertStatus(200);
        $resView->assertSee('EN VIVO');
        $resView->assertSee('Edición Mediodía');

        // Update edition name
        $this->actingAs($admin)->post("/rundown/{$rundown->id}/update-datetime", [
            'air_date'     => '2026-10-07',
            'air_time'     => '19:00:00',
            'episode_name' => 'Especial Elecciones',
        ]);

        $rundown->refresh();
        $this->assertEquals('Especial Elecciones', $rundown->getEditionTitle());
    }
}
