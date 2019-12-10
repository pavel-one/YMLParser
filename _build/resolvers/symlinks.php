<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/YMLParser/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/ymlparser')) {
            $cache->deleteTree(
                $dev . 'assets/components/ymlparser/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/ymlparser/', $dev . 'assets/components/ymlparser');
        }
        if (!is_link($dev . 'core/components/ymlparser')) {
            $cache->deleteTree(
                $dev . 'core/components/ymlparser/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/ymlparser/', $dev . 'core/components/ymlparser');
        }
    }
}

return true;