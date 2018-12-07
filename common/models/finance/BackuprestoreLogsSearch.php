<?php

namespace common\models\finance;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\finance\BackuprestoreLogs;

/**
 * BackuprestoreLogsSearch represents the model behind the search form about `common\models\finance\BackuprestoreLogs`.
 */
class BackuprestoreLogsSearch extends BackuprestoreLogs
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['activity', 'transaction_date', 'op_data', 'pi_data', 'sc_data', 'receipt_data', 'check_data', 'deposit_data', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BackuprestoreLogs::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'transaction_date' => $this->transaction_date,
        ]);

        $query->andFilterWhere(['like', 'activity', $this->activity])
            ->andFilterWhere(['like', 'op_data', $this->op_data])
            ->andFilterWhere(['like', 'pi_data', $this->pi_data])
            ->andFilterWhere(['like', 'sc_data', $this->sc_data])
            ->andFilterWhere(['like', 'receipt_data', $this->receipt_data])
            ->andFilterWhere(['like', 'check_data', $this->check_data])
            ->andFilterWhere(['like', 'deposit_data', $this->deposit_data])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
