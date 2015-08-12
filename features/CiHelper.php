<?php

use Behat\Behat\Event\ScenarioEvent;

if (!class_exists('CiHelper')) {
    class CiHelper
    {
        private static $lastContainerId;

        /**
         * @return bool
         */
        public static function isInternalCI()
        {
            return getenv('SONATA_CI_MODE') ? true : false;
        }

        /**
         * @return resource
         */
        public static function getClient()
        {
            $client = stream_socket_client("tcp://localhost:6666", $errno, $errorMessage);

            if ($client === false) {
                throw new UnexpectedValueException("Failed to connect: $errorMessage");
            }

            return $client;
        }

        /**
         * @param null $value
         *
         * @return bool
         */
        public static function state($value = null)
        {
            $stateFile = sprintf("/tmp/sonata_behat_test_%s.state", getmygid());

            if (!is_file($stateFile)) {
                file_put_contents($stateFile, "0");
            }

            if ($value === null) {
                return file_get_contents($stateFile) === "0" ? false : true;
            }

            file_put_contents($stateFile, $value === true ? "1" : "0");
        }

        public static function StopDB($containerId)
        {
            if (!$containerId) {
                return;
            }

            $client = self::getClient();

            if (!$client) {
                return;
            }

    //        echo "\033[36m >  " . strtr("Stopping the DB instance ...", array("\n" => "\n >  ")) . "\033[0m\n";
            fwrite($client, sprintf("STOP %s", $containerId));
            fread($client, 3); // ACK
            fclose($client);

            self::state(false);
        }

        public static function StartDB()
        {
            $client = self::getClient();

            if (!$client) {
                return;
            }

    //        echo "\033[36m >  " . strtr("Starting the DB instance ...", array("\n" => "\n >  ")) . "\033[0m\n";
            fwrite($client, "START");
            $data = fread($client, 68); // ACK
            fclose($client);

            // ACK 41591298e68abfdae4ae4a91f5206085f10914ee2947c808f58a5cc6c62e8334
            $containerId = substr($data, 4);

            self::state(true);

            return $containerId;
        }


        public static function run($event)
        {
            if (!self::isInternalCI()) {
                return;
            }

            if ($event instanceof ScenarioEvent && in_array("keep", $event->getScenario()->getOwnTags())) {
                if (self::state() === false) {
                    self::StartDB();
                }

    //            echo "\033[36m >  " . strtr("Keeping the DB instance ...", array("\n" => "\n >  ")) . "\033[0m\n";
                return;
            }

            self::StopDB(self::$lastContainerId);
            self::$lastContainerId = self::StartDB();
        }
    }
}