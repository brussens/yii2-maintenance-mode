<?php

namespace brussens\maintenance\commands;

use yii\helpers\Console;
use yii\console\Controller;
use yii\base\Module;
use brussens\maintenance\StateInterface;

class MaintenanceController extends Controller
{
    public $message = null;

    /**
     * @var StateInterface
     */
    protected $state;

    /**
     * Add the ability to specify a custom message
     * why the project is in maintenance mode
     * (and possibly how long it is estimated to stay in this mode)
     *
     * @param string $actionID
     *
     * @return array|string[]
     */
    public function options($actionID)
    {
        return ['message'];
    }

    /**
     * Aliases for --message option
     *
     * @return array
     */
    public function optionAliases()
    {
        return ['m' => 'message'];
    }

    public function __construct(string $id, Module $module, StateInterface $state, array $config = [])
    {
        $this->state = $state;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        if ($this->state->isEnabled()) {
            $enabled = $this->ansiFormat('ENABLED', Console::FG_RED);
            $this->stdout("Maintenance Mode is $enabled!\n");

            $this->displayCustomMessage();

            $this->stdout("Use:\nphp yii maintenance/disable\nto disable maintenance mode.\n");
        } else {
            $this->stdout("Maintenance Mode is disabled.\n");
            $this->stdout("Use:\nphp yii maintenance/enable\nto enable maintenance mode.\n");
        }

    }

    public function actionEnable()
    {
        $enabled = $this->ansiFormat('ENABLED', Console::FG_RED);
        if ($this->state->isEnabled()) {
            $this->stdout("The maintenance mode is already $enabled!\n");
            exit(1);
        }
        $this->state->enable($this->message);
        $this->stdout("Maintenance Mode has been $enabled!\n");
        $this->displayCustomMessage();
        $this->stdout("Use:\nphp yii maintenance/disable\nto disable maintenance mode.\n");
    }

    public function actionDisable()
    {
        if (! $this->state->isEnabled()) {
            $this->stdout(
                "The maintenance mode is not enabled and could therefore not be disabled anymore\n"
            );
            exit(1);
        }
        $this->state->disable();
        $this->stdout("Maintenance Mode has been disabled.\n");
        $this->stdout("Use:\nphp yii maintenance/enable\nto enable maintenance mode.\n");
    }

    private function displayCustomMessage(): void
    {
        if ($this->message) {
            $this->stdout(
                "The custom message that is being displayed to the users is: {$this->message}\n");
        } else {
            $this->stdout(
                "There has been no custom message provided to be displayed to the users.\n");
        }
    }
}

