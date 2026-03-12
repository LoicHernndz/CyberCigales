<?php

namespace Tests\Models\Lesson;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Models\Lesson\LessonProgress;
use Tests\Mocks\MockDatabase;

class LessonProgressTest extends TestCase
{
    private LessonProgress $progress;
    private MockDatabase $mockDB;

    protected function setUp(): void
    {
        $this->mockDB = new MockDatabase();

        // Skip constructor (ensureTable() touche la vraie DB)
        $ref = new \ReflectionClass(LessonProgress::class);
        $this->progress = $ref->newInstanceWithoutConstructor();
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->progress, $this->mockDB);
    }

    // =============================================
    // getRequiredLessons()
    // =============================================

    #[Test]
    public function getRequiredLessonsReturnsThreeLessons(): void
    {
        $lessons = $this->progress->getRequiredLessons();
        $this->assertCount(3, $lessons);
        $this->assertContains('cesar', $lessons);
        $this->assertContains('vigenere', $lessons);
        $this->assertContains('permutation', $lessons);
    }

    // =============================================
    // isCompleted()
    // =============================================

    #[Test]
    public function isCompletedReturnsTrueWhenFound(): void
    {
        $this->mockDB->singleReturn = (object)['id' => 1];
        $this->assertTrue($this->progress->isCompleted(1, 'cesar'));
    }

    #[Test]
    public function isCompletedReturnsFalseWhenNotFound(): void
    {
        $this->mockDB->singleReturn = null;
        $this->assertFalse($this->progress->isCompleted(1, 'cesar'));
    }

    // =============================================
    // getCompletedLessons()
    // =============================================

    #[Test]
    public function getCompletedLessonsReturnsSlugArray(): void
    {
        $this->mockDB->resultSetReturn = [
            (object)['lesson_slug' => 'cesar'],
            (object)['lesson_slug' => 'vigenere'],
        ];
        $result = $this->progress->getCompletedLessons(1);
        $this->assertSame(['cesar', 'vigenere'], $result);
    }

    #[Test]
    public function getCompletedLessonsReturnsEmptyWhenNone(): void
    {
        $this->mockDB->resultSetReturn = [];
        $result = $this->progress->getCompletedLessons(1);
        $this->assertSame([], $result);
    }

    // =============================================
    // areRequiredLessonsCompleted()
    // =============================================

    #[Test]
    public function areRequiredLessonsCompletedReturnsTrueWhenAllDone(): void
    {
        $this->mockDB->resultSetReturn = [
            (object)['lesson_slug' => 'cesar'],
            (object)['lesson_slug' => 'vigenere'],
            (object)['lesson_slug' => 'permutation'],
        ];
        $this->assertTrue($this->progress->areRequiredLessonsCompleted(1));
    }

    #[Test]
    public function areRequiredLessonsCompletedReturnsFalseWhenPartial(): void
    {
        $this->mockDB->resultSetReturn = [
            (object)['lesson_slug' => 'cesar'],
        ];
        $this->assertFalse($this->progress->areRequiredLessonsCompleted(1));
    }

    #[Test]
    public function areRequiredLessonsCompletedReturnsFalseWhenEmpty(): void
    {
        $this->mockDB->resultSetReturn = [];
        $this->assertFalse($this->progress->areRequiredLessonsCompleted(1));
    }

    // =============================================
    // getMissingLessons()
    // =============================================

    #[Test]
    public function getMissingLessonsReturnsAllWhenNoneCompleted(): void
    {
        $this->mockDB->resultSetReturn = [];
        $missing = $this->progress->getMissingLessons(1);
        $this->assertCount(3, $missing);
        $this->assertContains('César', $missing);
        $this->assertContains('Vigenère', $missing);
        $this->assertContains('Permutation', $missing);
    }

    #[Test]
    public function getMissingLessonsReturnsOnlyMissing(): void
    {
        $this->mockDB->resultSetReturn = [
            (object)['lesson_slug' => 'cesar'],
            (object)['lesson_slug' => 'permutation'],
        ];
        $missing = $this->progress->getMissingLessons(1);
        $this->assertCount(1, $missing);
        $this->assertContains('Vigenère', $missing);
    }

    #[Test]
    public function getMissingLessonsReturnsEmptyWhenAllCompleted(): void
    {
        $this->mockDB->resultSetReturn = [
            (object)['lesson_slug' => 'cesar'],
            (object)['lesson_slug' => 'vigenere'],
            (object)['lesson_slug' => 'permutation'],
        ];
        $this->assertSame([], $this->progress->getMissingLessons(1));
    }

    // =============================================
    // markCompleted() et resetProgress() — vérifie les queries
    // =============================================

    #[Test]
    public function markCompletedExecutesInsertQuery(): void
    {
        $this->mockDB->returnValue = true;
        $this->progress->markCompleted(1, 'cesar');
        $this->assertNotEmpty($this->mockDB->queries);
        $this->assertStringContainsString('INSERT INTO lesson_progress', $this->mockDB->queries[0]);
    }

    #[Test]
    public function resetProgressExecutesDeleteQuery(): void
    {
        $this->mockDB->returnValue = true;
        $this->progress->resetProgress(1);
        $this->assertNotEmpty($this->mockDB->queries);
        $this->assertStringContainsString('DELETE FROM lesson_progress', $this->mockDB->queries[0]);
    }
}
