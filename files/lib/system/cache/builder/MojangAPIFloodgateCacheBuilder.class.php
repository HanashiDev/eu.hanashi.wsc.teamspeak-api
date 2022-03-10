<?php
namespace wcf\system\cache\builder;

/**
 * MinecraftHandler abstract class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Cache\Builder
 */
class MojangAPIFloodgateCacheBuilder extends AbstractCacheBuilder {

    /**
     * Time in seconds until $max_requests gets reset
     * @var int
     */
    protected $reset_time = 600;

    /**
     * Max requests in $reset_time
     * @var int
     */
    protected $max_requests = 600;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters = [])
    {
        $data = [];

        if (isset($parameters['count']) && is_int($parameters['count'])) {
            $data['count'] = $parameters['count'];
        } else {
            $data['count'] = 0;
        }
        if (isset($parameters['created']) && is_int($parameters['created'])) {
            $data['created'] = $parameters['created'];
        } else {
            $data['created'] = TIME_NOW;
        }
        $data['last'] = TIME_NOW;

        return $data;
    }

    /**
     * Checks if Mojang-API can be used.
     *
     * @return bool true if a a new try can be startet.
     */
    public function canExecute()
    {
        if ($this->getData()['count'] >= $this->max_requests) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if Mojang-API can be used.
     *
     * @return bool true if a a new try can be startet.
     *              Automatically adds a try to the counter and resets time possable.
     *              Otherwise false.
     */
    public function try()
    {
        $data = $this->getData();

        if ((TIME_NOW - $data['created']) >= $this->reset_time) {
            $this->reset([
                'count' => 1
            ]);
        } else {
            $this->reset([
                'count' => $data['count'] + 1,
                'created' => $data['created']
            ]);
        }

        if ($data['count'] <= $this->max_requests) {
            return true;
        } else {
            return false;
        }
    }
}
