<?php

declare(strict_types=1);

namespace App\Tests\Unit\Serializer\Normalizer;

use App\Entity\Project;
use App\Serializer\Normalizer\ProjectNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProjectNormalizerTest extends TestCase
{
    private ProjectNormalizer $normalizer;

    protected function setUp(): void
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, new MetadataAwareNameConverter($classMetadataFactory), null, $extractor);
        $this->normalizer = new ProjectNormalizer($objectNormalizer);
    }

    // ========================================================================
    // Project basics
    // ========================================================================

    public function testDenormalizeProjectIdentity(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertSame(99001, $project->getExternalId());
        $this->assertFalse($project->getArchived());
    }

    public function testDenormalizeProjectLocation(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertSame('Haarlem', $project->getCity());
        $this->assertSame('2011AB', $project->getZipcode());
        $this->assertSame('Noord-Holland', $project->getProvince());
    }

    public function testDenormalizeProjectTitle(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertSame('Nieuwbouwproject De Haven, Haarlem', $project->getTitle());
    }

    public function testDenormalizeProjectDescription(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertSame('Prachtig nieuwbouwproject aan de haven van Haarlem.', $project->getDescription());
    }

    public function testDenormalizeProjectCategory(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertSame('Koop', $project->getCategory());
    }

    // ========================================================================
    // Areas
    // ========================================================================

    public function testDenormalizeProjectLivingArea(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertSame('80 tot 150', $project->getLivingArea());
    }

    public function testDenormalizeProjectPlot(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertSame('120 tot 200', $project->getPlot());
    }

    // ========================================================================
    // Status logic
    // ========================================================================

    public function testDenormalizeProjectStatusBouwGestart(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        // datumStartVerkoop (2024-01-15) is in the past
        // datumStartBouw (2024-06-01) is in the past (we're testing in 2026)
        // opleveringsdatum (2025-12-01) is in the past
        // So status should be "Oplevering gestart"
        $this->assertSame('Oplevering gestart', $project->getReadableStatus());
        $this->assertSame('BESCHIKBAAR', $project->getStatus());
    }

    public function testDenormalizeProjectStatusVerkocht(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $data['project']['algemeen']['status'] = 'VERKOCHT';
        $project = $this->normalizer->denormalize($data, Project::class);

        // VERKOCHT overrides everything
        $this->assertSame('Verkocht', $project->getReadableStatus());
    }

    // ========================================================================
    // Media
    // ========================================================================

    public function testDenormalizeProjectMedia(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertCount(2, $project->getMedia());
        // Main image should be the HOOFDFOTO
        $mainImage = $project->getMainImage();
        $this->assertNotNull($mainImage);
        $this->assertNotEmpty($project->getMediaHash());
    }

    public function testDenormalizeProjectDates(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertSame('2024-01-10', $project->getCreatedAt()->format('Y-m-d'));
        $this->assertSame('2024-05-20', $project->getUpdatedAt()->format('Y-m-d'));
    }

    // ========================================================================
    // Construction types
    // ========================================================================

    public function testDenormalizeConstructionTypes(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $types = $project->getConstructionTypes();
        $this->assertCount(1, $types);

        $typeA = $types->first();
        $this->assertSame(5001, $typeA->getExternalId());
        $this->assertSame('Type A - Tussenwoning', $typeA->getTitle());
        $this->assertSame('Tussenwoning', $typeA->getType());
        $this->assertSame(5, $typeA->getRooms()); // 2 + 3
        $this->assertSame('95', $typeA->getLivingArea());
    }

    public function testDenormalizeConstructionNumbers(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $typeA = $project->getConstructionTypes()->first();
        $numbers = $typeA->getConstructionNumbers();
        $this->assertCount(2, $numbers);

        // First construction number
        $bn1 = $numbers->filter(fn ($n) => $n->getExternalId() === 7001)->first();
        $this->assertSame('Havenstraat', $bn1->getTitle());
        $this->assertSame('A++', $bn1->getEnergyClass());
        $this->assertSame(95, $bn1->getLivingArea());
        $this->assertSame('BESCHIKBAAR', $bn1->getStatus());
        $this->assertSame('Beschikbaar', $bn1->getReadableStatus());
        $this->assertSame('v.o.n.', $bn1->getPriceCondition());
        $this->assertSame(5, $bn1->getRooms());
        $this->assertSame(2, $bn1->getBedrooms());
        $this->assertSame('Bouwnummer 1 aan de Havenstraat.', $bn1->getDescription());

        // Price is stored as Money object (cents)
        $this->assertSame('39500000', $bn1->getPrice()->getAmount());

        // Second construction number - sold
        $bn2 = $numbers->filter(fn ($n) => $n->getExternalId() === 7002)->first();
        $this->assertSame('VERKOCHT', $bn2->getStatus());
        $this->assertSame('Verkocht', $bn2->getReadableStatus());
    }

    public function testDenormalizeConstructionNumberMedia(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $typeA = $project->getConstructionTypes()->first();
        $bn1 = $typeA->getConstructionNumbers()->filter(fn ($n) => $n->getExternalId() === 7001)->first();

        $this->assertCount(1, $bn1->getMedia());
        $this->assertNotEmpty($bn1->getMediaHash());
    }

    // ========================================================================
    // Rooms aggregation
    // ========================================================================

    public function testDenormalizeProjectRoomsRange(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        // Only one bouwtype with 5 rooms, so lowest == highest
        $this->assertSame('5', $project->getRooms());
    }

    // ========================================================================
    // Raw data arrays
    // ========================================================================

    public function testDenormalizeProjectRawArrays(): void
    {
        $data = $this->loadFixture('realworks_v2_project.json');
        $project = $this->normalizer->denormalize($data, Project::class);

        $this->assertIsArray($project->getAlgemeen());
        $this->assertSame('KOOP', $project->getAlgemeen()['koopOfHuur']);
        $this->assertIsArray($project->getDiversen());
        $this->assertSame('Intern project', $project->getDiversen()['notities']);
    }

    private function loadFixture(string $filename): array
    {
        $path = __DIR__ . '/../../../Fixtures/' . $filename;
        return json_decode(file_get_contents($path), true);
    }
}
