<?php

declare(strict_types=1);

namespace App\Tests\Unit\Serializer\Normalizer;

use App\Entity\Property;
use App\Serializer\Normalizer\PropertyNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PropertyNormalizerTest extends TestCase
{
    private PropertyNormalizer $normalizer;

    protected function setUp(): void
    {
        $objectNormalizer = $this->createMock(NormalizerInterface::class);
        // The mock needs to implement both interfaces
        $mock = $this->createMock(ObjectNormalizerStub::class);
        $this->normalizer = new PropertyNormalizer($mock);
    }

    private function loadFixture(string $filename): array
    {
        $path = __DIR__ . '/../../../Fixtures/' . $filename;
        return json_decode(file_get_contents($path), true);
    }

    // ========================================================================
    // Sale property (koop)
    // ========================================================================

    public function testDenormalizeSalePropertyAddress(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame(42, $property->getHouseNumber());
        $this->assertSame('A', $property->getHouseNumberAddition());
        $this->assertSame('Amsterdam', $property->getCity());
        $this->assertSame('1012AB', $property->getZip());
        $this->assertSame('Keizersgracht', $property->getStreet());
        $this->assertSame('Keizersgracht 42A, Amsterdam', $property->getAddress());
    }

    public function testDenormalizeSalePropertyTitle(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame('Keizersgracht 42A, Amsterdam', $property->getTitle());
    }

    public function testDenormalizeSalePropertyGenericFields(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame(12345, $property->getExternalId());
        $this->assertFalse($property->getArchived());
        $this->assertSame(1920, $property->getBuildYear());
        $this->assertSame('A+', $property->getEnergyClass());
        $this->assertSame(120, $property->getLivingArea());
        $this->assertSame('85', $property->getPlot()); // totaleWoonkameroppervlakte takes priority
    }

    public function testDenormalizeSalePropertyDescription(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        // eigenSiteTekst takes priority over aanbiedingstekst
        $this->assertSame('Prachtige grachtenwoning in het hart van Amsterdam.', $property->getDescription());
    }

    public function testDenormalizeSalePropertyPricing(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame(750000, $property->getPrice());
        $this->assertSame('k.k.', $property->getPriceCondition());
        $this->assertSame('Koop', $property->getCategory());
        $this->assertSame('BESCHIKBAAR', $property->getStatus());
        $this->assertSame('Beschikbaar', $property->getReadableStatus());
    }

    public function testDenormalizeSalePropertyRooms(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame(3, $property->getBedrooms()); // 2 + 1
        $this->assertSame(6, $property->getRooms());    // 4 + 2
    }

    public function testDenormalizeSalePropertyMedia(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertCount(3, $property->getMedia());
        // Main image should be the HOOFDFOTO
        $this->assertSame('HOOFDFOTO', $property->getImage()['soort']);
        $this->assertSame('https://example.com/main.jpg', $property->getImage()['url']);
        $this->assertNotEmpty($property->getMediaHash());
    }

    public function testDenormalizeSalePropertyDates(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame('2024-01-15', $property->getCreatedAt()->format('Y-m-d'));
        $this->assertSame('2024-06-01', $property->getUpdatedAt()->format('Y-m-d'));
    }

    public function testDenormalizeSalePropertyRawArrays(): void
    {
        $data = $this->loadFixture('realworks_v2_property.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertIsArray($property->getAlgemeen());
        $this->assertSame(1920, $property->getAlgemeen()['bouwjaar']);
        $this->assertIsArray($property->getFinancieel());
        $this->assertSame(750000, $property->getFinancieel()['overdracht']['koopprijs']);
        $this->assertIsArray($property->getTeksten());
        $this->assertIsArray($property->getEtages());
        $this->assertCount(2, $property->getEtages());
        $this->assertIsArray($property->getBuitenruimte());
    }

    // ========================================================================
    // Rental property (huur)
    // ========================================================================

    public function testDenormalizeRentalPropertyPricing(): void
    {
        $data = $this->loadFixture('realworks_v2_property_rental.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame(1500, $property->getPrice());
        $this->assertSame('p.m.', $property->getPriceCondition());
        $this->assertSame('Huur', $property->getCategory());
    }

    public function testDenormalizeRentalPropertyFallbackDescription(): void
    {
        $data = $this->loadFixture('realworks_v2_property_rental.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        // eigenSiteTekst is null, falls back to aanbiedingstekst
        $this->assertSame('Modern appartement in het centrum van Rotterdam.', $property->getDescription());
    }

    public function testDenormalizeRentalPropertyAddress(): void
    {
        $data = $this->loadFixture('realworks_v2_property_rental.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame(10, $property->getHouseNumber());
        $this->assertNull($property->getHouseNumberAddition());
        $this->assertSame('Coolsingel 10, Rotterdam', $property->getAddress());
    }

    // ========================================================================
    // Zero house number edge case
    // ========================================================================

    public function testDenormalizeZeroHouseNumber(): void
    {
        $data = $this->loadFixture('realworks_v2_property_zero_housenumber.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        // huisnummer=0 is falsy so it never gets set; numberIsZero check doesn't trigger
        // This means the else branch runs, producing a trailing space before the comma
        // This is a known quirk in the current code
        $this->assertNull($property->getHouseNumber());
        $this->assertNull($property->getHouseNumberAddition());
        $this->assertSame('Landgoed De Bilt , Utrecht', $property->getAddress());
        $this->assertSame('Landgoed De Bilt , Utrecht', $property->getTitle());
    }

    public function testDenormalizeZeroHouseNumberPlotFallback(): void
    {
        $data = $this->loadFixture('realworks_v2_property_zero_housenumber.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        // totaleWoonkameroppervlakte is null, falls back to totaleKadestraleOppervlakte
        $this->assertSame('500', $property->getPlot());
    }

    public function testDenormalizeZeroBuildYear(): void
    {
        $data = $this->loadFixture('realworks_v2_property_zero_housenumber.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        // bouwjaar = 0 should not be set
        $this->assertNull($property->getBuildYear());
    }

    public function testDenormalizeEmptyMediaFallback(): void
    {
        $data = $this->loadFixture('realworks_v2_property_zero_housenumber.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertEmpty($property->getMedia());
        $this->assertNull($property->getImage());
    }

    public function testDenormalizeEmptyEtages(): void
    {
        $data = $this->loadFixture('realworks_v2_property_zero_housenumber.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame(0, $property->getBedrooms());
        $this->assertSame(0, $property->getRooms());
    }

    public function testDenormalizeStatusTranslation(): void
    {
        $data = $this->loadFixture('realworks_v2_property_zero_housenumber.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame('ONDER_BOD', $property->getStatus());
        $this->assertSame('Onder bod', $property->getReadableStatus());
    }

    public function testDenormalizeVonCondition(): void
    {
        $data = $this->loadFixture('realworks_v2_property_zero_housenumber.json');
        $property = $this->normalizer->denormalize($data, Property::class);

        $this->assertSame('v.o.n.', $property->getPriceCondition());
    }
}

/**
 * Stub for creating a mock that implements both interfaces.
 */
abstract class ObjectNormalizerStub implements NormalizerInterface, DenormalizerInterface
{
}
