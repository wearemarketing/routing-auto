<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\RoutingAuto;

use Metadata\MetadataFactoryInterface;

/**
 * Class responsible for creating all the implied UriContext objects for
 * the given UriContextColletion.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class UriContextCollectionBuilder
{
    /**
     * @var MetadataFactoryInterface
     */
    protected $metadataFactory;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    public function __construct(MetadataFactoryInterface $metadataFactory, AdapterInterface $adapter)
    {
        $this->metadataFactory = $metadataFactory;
        $this->adapter = $adapter;
    }

    /**
     * Populates an empty UriContextCollection with UriContexts.
     *
     * @param $uriContextCollection UriContextCollection
     */
    public function build(UriContextCollection $uriContextCollection)
    {
        $subjectObject = $uriContextCollection->getSubjectObject();
        $realClassName = $this->adapter->getRealClassName(get_class($subjectObject));
        $metadata = $this->metadataFactory->getMetadataForClass($realClassName);

        // TODO: This is where we will call $metadata->getUriSchemas() which will return an
        //       array of URI schemas (inc. the "template", TP configs and CR configs).
        $definitions = $metadata->getAutoRouteDefinitions();

        foreach ($definitions as $definition) {
            $locales = $this->adapter->getLocales($subjectObject) ?: array(null);
            foreach ($locales as $locale) {
                // create and add uri context to stack
                $uriContext = $uriContextCollection->createUriContext(
                    $definition->getUriSchema(),
                    $definition->getDefaults(),
                    $metadata->getTokenProviders(),
                    $metadata->getConflictResolver(),
                    $locale
                );
                $uriContextCollection->addUriContext($uriContext);
            }
        }
    }
}
