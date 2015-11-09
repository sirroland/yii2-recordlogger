<?php

	namespace dvlp\recordlogger\behaviors;

	use dvlp\recordlogger\models\RecordLogger;
	use common\helpers\GHelper;
	use yii\base\Behavior;
	use yii\base\Exception;
	use yii\db\ActiveRecord;
	use yii\helpers\Json;


	/**
	 * Class CopyAttributesBehavior
	 * @package common\behaviors
	 */
	class RecordLoggerBehavior extends Behavior {

		public $appIDs       = ['app-backend'];

		private $data_prev    = null;
		private $data_current = null;
		private $attributes   = [];


		public function events() {
			return [
				ActiveRecord::EVENT_AFTER_INSERT  => 'afterInsert',
				ActiveRecord::EVENT_AFTER_FIND    => 'afterFind',
				ActiveRecord::EVENT_AFTER_UPDATE  => 'afterUpdate',
				ActiveRecord::EVENT_AFTER_DELETE  => 'afterDelete',

			];
		}

		public function Init() {
			if(!is_array($this->appIDs))
				$this->appIDs = [$this->appIDs];

			if(!in_array(\Yii::$app->id, $this->appIDs))
				return false;

			$this->attributes = [
				'created_at'      => GHelper::dateTime(),
				'app_id' => \Yii::$app->id,
			];
			if(!empty(\Yii::$app->user)) {
				$this->attributes['user_id'] = \Yii::$app->user->id;
			}
		}

		public function afterInsert() {
			$this->attributes = array_merge($this->attributes, [
				'type'      => 'insert',
				'data_current'  => Json::encode($this->owner->attributes),
			]);
			$this->saveData();
		}

		public function afterFind() {
			$this->data_prev = $this->owner->attributes;
		}
		public function afterUpdate() {
			$this->attributes = array_merge($this->attributes, [
				'type'         => 'update',
				'data_current' => Json::encode($this->owner->attributes),
				'data_prev'    => Json::encode($this->data_prev),
			]);
			$this->saveData();
		}

		public function afterDelete() {
			$this->attributes = array_merge($this->attributes,[
				'type'         => 'delete',
				'data_current' => Json::encode($this->owner->attributes),
			]);
			$this->saveData();
		}

		private function saveData() {
			if(!in_array(\Yii::$app->id, $this->appIDs))
				return false;
			$model = new RecordLogger();

			$model->model_name      = $this->owner->className();
			$model->table_name      = $this->owner->tableName();
			$model->record_id  =  Json::encode($this->owner->getPrimaryKey(true));
			$model->attributes = $this->attributes;
			$model->created_at = date('Y-m-d H:i:s');
			$model->save();
		}

	}