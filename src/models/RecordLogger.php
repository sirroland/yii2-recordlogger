<?php

	namespace dvlp\recordlogger\models;

	use Yii;
	use yii\behaviors\TimestampBehavior;

	/**
	 * This is the model class for table "{{%record_logger}}".
	 *
	 * @property integer $id
	 * @property string $model_name
	 * @property string $table_name
	 * @property string $type
	 * @property string $data_prev
	 * @property string $data_current
	 * @property string $created_at
	 * @property string $app_id
	 * @property integer $user_id
	 * @property integer $record_id
	 */
	class RecordLogger extends \yii\db\ActiveRecord
	{
		/**
		 * @inheritdoc
		 */
		public static function tableName()
		{
			return '{{%record_logger}}';
		}

		/**
		 * @inheritdoc
		 */
		public function rules()
		{
			return [
				[['model_name', 'type', 'data_current', 'record_id', 'table_name'], 'required'],
				[['id', 'user_id'], 'integer'],
				[['data_prev', 'data_current'], 'string'],
				[['created_at'], 'safe'],
				[['model_name', 'table_name', 'type', 'app_id', 'record_id'], 'string', 'max' => 255]
			];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels()
		{
			return [
				'id' => 'ID',
				'model_name' => 'Model',
				'table_name' => 'Table',
				'type' => 'Type',
				'data_prev' => 'Old Data',
				'data_current' => 'New Data',
				'created_at' => 'Created date',
				'user_id' => 'User ID',
				'record_id' => 'Record ID',
				'app_id' => 'App ID',
			];
		}
	}
