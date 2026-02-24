<?php

declare(strict_types=1);

namespace App\Tests\Unit\Serializer\Normalizer;

use App\Entity\BogObject;
use App\Serializer\Normalizer\BogObjectNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BogObjectNormalizerTest extends TestCase
{
    private BogObjectNormalizer $normalizer;

    protected function setUp(): void
    {
        $mock = $this->createMock(ObjectNormalizerStub::class);
        $this->normalizer = new BogObjectNormalizer($mock);
    }

    private function loadFixture(string $filename): array
    {
        $path = __DIR__ . '/../../../Fixtures/' . $filename;
        return json_decode(file_get_contents($path), true);
    }

    // ========================================================================
    // Address
    // ========================================================================

    public function testDenormalizeBogAddress(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame('15B', $bog->getHouseNumber());
        $this->assertSame('Den Haag', $bog->getCity());
        $this->assertSame('2500AA', $bog->getZipCode());
        $this->assertSame('Lange Voorhout', $bog->getStreet());
        $this->assertSame('Nederland', $bog->getCountry());
    }

    public function testDenormalizeBogTitle(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame('Lange Voorhout 15B, Den Haag', $bog->getTitle());
    }

    // ========================================================================
    // Identity & generic
    // ========================================================================

    public function testDenormalizeBogIdentity(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame(55001, $bog->getExternalId());
        $this->assertFalse($bog->getArchived());
    }

    public function testDenormalizeBogMainFunction(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame('Kantoorruimte', $bog->getMainFunction());
    }

    public function testDenormalizeBogDescription(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        // eigenSiteTekst takes priority
        $this->assertSame('Representatief kantoor op toplocatie.', $bog->getDescription());
    }

    public function testDenormalizeBogBuildYear(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame(1890, $bog->getBuildYear());
    }

    public function testDenormalizeBogEnergyClass(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame('C', $bog->getEnergyClass());
    }

    // ========================================================================
    // Pricing
    // ========================================================================

    public function testDenormalizeBogRentalPricing(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame(2500, $bog->getPrice());
        $this->assertSame('p.m.', $bog->getPriceCondition());
        $this->assertSame('Huur', $bog->getCategory());
    }

    public function testDenormalizeBogServiceCosts(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame(350, $bog->getServiceCostPrice());
        $this->assertSame('p.m.', $bog->getServiceCostCondition());
        $this->assertTrue($bog->getServiceCostVAT());
    }

    public function testDenormalizeBogStatus(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame('BESCHIKBAAR', $bog->getStatus());
        $this->assertSame('Beschikbaar', $bog->getReadableStatus());
    }

    // ========================================================================
    // Functions & facilities
    // ========================================================================

    public function testDenormalizeBogFunctionsFiltersInactive(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        // Only the active kantoorruimte function should remain
        $functions = $bog->getFunctions();
        $this->assertCount(1, $functions);
        $this->assertArrayHasKey('kantoorruimte', $functions[0]);
    }

    public function testDenormalizeBogPlot(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        // From kantoorruimte oppervlakte
        $this->assertSame('250', $bog->getPlot());
    }

    public function testDenormalizeBogFacilities(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertStringContainsString('Lift', $bog->getFacilities());
        $this->assertStringContainsString('Receptie', $bog->getFacilities());
        $this->assertStringContainsString('Systeemplafond', $bog->getFacilities());
        $this->assertStringContainsString('Te openen ramen', $bog->getFacilities());
    }

    public function testDenormalizeBogNumberOfFloors(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame(3, $bog->getNumberOfFloors());
    }

    // ========================================================================
    // Location details
    // ========================================================================

    public function testDenormalizeBogAccessibility(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $accessibility = $bog->getAccessibility();
        $this->assertStringContainsString('Bushalte', $accessibility);
        $this->assertStringContainsString('NS Station', $accessibility);
        $this->assertStringContainsString('Snelwegafrit', $accessibility);
    }

    public function testDenormalizeBogLocalAmentities(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $amentities = $bog->getLocalAmentities();
        $this->assertStringContainsString('Restaurant', $amentities);
        $this->assertStringContainsString('Winkel', $amentities);
    }

    public function testDenormalizeBogParking(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertIsArray($bog->getParking());
        $this->assertSame('PARKEERGARAGE', $bog->getParking()[0]['soort']);
    }

    // ========================================================================
    // Media
    // ========================================================================

    public function testDenormalizeBogMedia(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertCount(2, $bog->getMedia());
        $this->assertSame('HOOFDFOTO', $bog->getImage()['soort']);
        $this->assertSame('https://example.com/bog-main.jpg', $bog->getImage()['url']);
        $this->assertNotEmpty($bog->getMediaHash());
    }

    // ========================================================================
    // Dates
    // ========================================================================

    public function testDenormalizeBogDates(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame('2024-02-20', $bog->getCreatedAt()->format('Y-m-d'));
        $this->assertSame('2024-06-10', $bog->getUpdatedAt()->format('Y-m-d'));
    }

    // ========================================================================
    // Raw data arrays
    // ========================================================================

    public function testDenormalizeBogRawArrays(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertIsArray($bog->getFinance());
        $this->assertArrayHasKey('overdracht', $bog->getFinance());
        $this->assertIsArray($bog->getDiversen());
        $this->assertIsArray($bog->getKadaster());
    }

    // ========================================================================
    // BOG sale property
    // ========================================================================

    public function testDenormalizeBogSaleProperty(): void
    {
        $data = $this->loadFixture('realworks_v2_bog_object.json');
        // Override to make it a sale property
        $data['financieel']['overdracht']['koopEnOfHuur']['koopprijs'] = 950000;
        $data['financieel']['overdracht']['koopEnOfHuur']['huurprijs'] = 0;
        $data['financieel']['overdracht']['koopEnOfHuur']['koopconditie'] = 'KOSTEN_KOPER';
        $data['financieel']['overdracht']['koopEnOfHuur']['huurconditie'] = null;
        $data['financieel']['overdracht']['koopEnOfHuur']['servicekosten'] = 0;
        $data['financieel']['overdracht']['koopEnOfHuur']['servicekostenconditie'] = null;
        $bog = $this->normalizer->denormalize($data, BogObject::class);

        $this->assertSame(950000, $bog->getPrice());
        $this->assertSame('k.k.', $bog->getPriceCondition());
        $this->assertSame('Koop', $bog->getCategory());
    }
}
