<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\Unit\Translator\Adapter;

use Doctrine\Common\Persistence\ManagerRegistry;
use Textmaster\Model\ProjectInterface;
use Textmaster\Translator\Adapter\GedmoTranslatableAdapter;

class GedmoTranslatableAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateSameLocale()
    {
        $managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $listenerMock = $this->createMock('Gedmo\Translatable\TranslatableListener');

        $translatableMock = $this->createPartialMock('Gedmo\Translatable\Translatable', ['getName', 'getId']);
        $documentMock = $this->createPartialMock('Textmaster\Model\Document', ['getProject', 'save']);
        $projectMock = $this->createPartialMock('Textmaster\Model\Project', ['getLanguageFrom', 'getActivity']);

        $translatableMock->expects($this->once())
            ->method('getName')
            ->willReturn('name');
        $translatableMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $documentMock->expects($this->once())
            ->method('getProject')
            ->willReturn($projectMock);

        $projectMock->expects($this->once())
            ->method('getLanguageFrom')
            ->willReturn('en');

        $projectMock->expects($this->exactly(2))
            ->method('getActivity')
            ->willReturn(ProjectInterface::ACTIVITY_TRANSLATION);

        $listenerMock->expects($this->once())
            ->method('getListenerLocale')
            ->willReturn('en');

        $adapter = new GedmoTranslatableAdapter($managerRegistryMock, $listenerMock);
        $adapter->push($translatableMock, ['name'], $documentMock);
    }

    /**
     * @test
     */
    public function shouldCreateDifferentLocale()
    {
        $managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $listenerMock = $this->createMock('Gedmo\Translatable\TranslatableListener');

        $translatableMock = $this->createPartialMock('Gedmo\Translatable\Translatable', ['setLocale', 'getName', 'getId']);
        $documentMock = $this->createPartialMock('Textmaster\Model\Document', ['getProject', 'save']);
        $projectMock = $this->createPartialMock('Textmaster\Model\Project', ['getLanguageFrom']);
        $entityManagerMock = $this->createMock('Doctrine\Common\Persistence\ObjectManager');

        $translatableMock->expects($this->once())
            ->method('getName')
            ->willReturn('name');
        $translatableMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $documentMock->expects($this->once())
            ->method('getProject')
            ->willReturn($projectMock);

        $projectMock->expects($this->once())
            ->method('getLanguageFrom')
            ->willReturn('en');

        $listenerMock->expects($this->once())
            ->method('getListenerLocale')
            ->willReturn('fr');

        $managerRegistryMock->expects($this->once())
            ->method('getManagerForClass')
            ->willReturn($entityManagerMock);

        $entityManagerMock->expects($this->once())
            ->method('refresh');

        $adapter = new GedmoTranslatableAdapter($managerRegistryMock, $listenerMock);
        $adapter->push($translatableMock, ['name'], $documentMock);
    }
}
