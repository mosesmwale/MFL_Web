<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use backend\models\User;
use yii\data\ActiveDataProvider;
use kartik\grid\GridView;

$this->title = 'Home';
$this->params['breadcrumbs'][] = $this->title;
//GRZ Facilities
$public_count = backend\models\Facility::find()
        ->cache(Yii::$app->params['cache_duration'])
        ->where(['ownership_type' => 1])
        ->count();
$public_count_active = backend\models\Facility::find()
        ->cache(Yii::$app->params['cache_duration'])
        ->where(['ownership_type' => 1])
        ->andWhere(['status' => 1])
        ->count();

// Private
$_private_count = backend\models\Facility::find()
        ->cache(Yii::$app->params['cache_duration'])
        ->where(['IN', 'ownership_type', [2]])
        ->count();
// Private
$_private_count_active = backend\models\Facility::find()
        ->cache(Yii::$app->params['cache_duration'])
        ->where(['IN', 'ownership_type', [2]])
        ->andWhere(['status' => 1])
        ->count();

$count = 0;
$facilityDataProviderNationalApproval = "";
$provinceApprovalDataProvider = "";

if (User::userIsAllowedTo('Approve facility - Province') && Yii::$app->user->identity->user_type == "Province") {
    $distric_model = backend\models\Districts::find()
                    ->where(['province_id' => Yii::$app->user->identity->province_id])
                    ->asArray()->all();
    if (!empty($distric_model)) {
        $district_arr = [];
        foreach ($distric_model as $district) {
            array_push($district_arr, $district['id']);
        }

        $query = \backend\models\Facility::find()
                ->where(['status' => 0])
                ->andWhere(['province_approval_status' => 0])
                ->andWhere(['IN', 'district_id', $district_arr]);
        $provinceApprovalDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($provinceApprovalDataProvider->count > 0) {
            $count++;
        }
    }
}



if (User::userIsAllowedTo('Approve facility - National')) {
    $query = \backend\models\Facility::find()
            ->where(['status' => 0,])
            ->andWhere(['province_approval_status' => 1])
            ->andWhere(['national_approval_status' => 0]);
    $facilityDataProviderNationalApproval = new ActiveDataProvider([
        'query' => $query,
    ]);
    if ($facilityDataProviderNationalApproval->count > 0) {
        $count++;
    }
}

//Show rejected for district users
$facilityDataProviderRejected = "";
if (Yii::$app->user->identity->user_type == "District") {
    $query = \backend\models\Facility::find()
            ->where(['status' => 0,])
            ->andWhere(['province_approval_status' => 2])
            ->andWhere(['created_by' => Yii::$app->user->identity->id])
            ->andWhere(['national_approval_status' => 2]);
    $facilityDataProviderRejected = new ActiveDataProvider([
        'query' => $query,
    ]);
    if ($facilityDataProviderRejected->count > 0) {
        $count++;
    }
}
//Show rejected for province users
$facilityDataProviderRejectedProvince = "";
if (Yii::$app->user->identity->user_type == "Province") {
    $distric_model = backend\models\Districts::find()
                    ->where(['province_id' => Yii::$app->user->identity->province_id])
                    ->asArray()->all();
    if (!empty($distric_model)) {
        $district_arr = [];
        foreach ($distric_model as $district) {
            array_push($district_arr, $district['id']);
        }

        $query = \backend\models\Facility::find()
                ->where(['status' => 0,])
                ->andWhere(['province_approval_status' => 2])
                ->andWhere(['IN', 'district_id', $district_arr])
                ->andWhere(['national_approval_status' => 2]);
        $facilityDataProviderRejectedProvince = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($facilityDataProviderRejectedProvince->count > 0) {
            $count++;
        }
    }
}
?>
<!-- /.row -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="far fa-hospital"></i></span>

                <div class="info-box-content">
                    <span class="info-box-number"> <?= $public_count ?></span>

                    <div class="progress">
                        <div class="progress-bar" style="width:100%"></div>
                    </div>
                    <span class="progress-description text-sm">
                        Public Facilities
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-gradient-indigo">
                <span class="info-box-icon"><i class="fas fa-hospital-symbol"></i></span>

                <div class="info-box-content">
                    <span class="info-box-number"><?= $_private_count ?></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description text-sm">
                        Private Facilities
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check"></i></span>

                <div class="info-box-content">
                    <span class="info-box-number"><?= $public_count_active ?></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description text-sm">
                        Active Public Facilities
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>

                <div class="info-box-content">
                    <span class="info-box-number"><?= $_private_count_active ?></span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description text-sm">
                        Active private Facilities
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Your tasks</h5>
            </div>
            <div class="card-body">
                <?php
                if ($count > 0) {

                    //Review
                    if (!empty($provinceApprovalDataProvider) && $provinceApprovalDataProvider->count > 0) {
                        echo '<h5>New facilities for province approval</h5>
                        <p>Instructions</p>
                        <ol>
                            <li>Below facilities need province verification and approval</li>
                            <li>Click the "Verify" link under the Action column to go to the verification page</li>
                        </ol>
                        <hr class="dotted short">';

                        echo GridView::widget([
                            'dataProvider' => $provinceApprovalDataProvider,
                            'hover' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' => 'name',
                                    'filter' => false,
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'district_id',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\Districts::findOne($model->district_id)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'type',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\Facilitytype::findOne($model->type)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'ownership',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\FacilityOwnership::findOne($model->ownership)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'format' => 'raw',
                                    'attribute' => 'operational_status',
                                    'filter' => \false,
                                    'value' => function ($model) {
                                        $name = backend\models\Operationstatus::findOne($model->operational_status)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'Action',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::a("Verify", ["facilities/approve-facility-province", 'id' => $model->id]);
                                    }
                                ]
                            ],
                        ]);
                    }
                    //Approval
                    if (!empty($facilityDataProviderNationalApproval) && $facilityDataProviderNationalApproval->count > 0) {
                        echo '<h5>New facilities for National approval</h5>
                <p>Instructions</p>
                <ol>
                    <li>Below facilities need national approval for them to be active</li>
                    <li>Click the "Approve" link under the Action column to go to the approval page</li>
                </ol>
                <hr class="dotted short">';

                        echo GridView::widget([
                            'dataProvider' => $facilityDataProviderNationalApproval,
                            'hover' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' => 'name',
                                    'filter' => false,
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'district_id',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\Districts::findOne($model->district_id)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'type',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\Facilitytype::findOne($model->type)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'ownership',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\FacilityOwnership::findOne($model->ownership)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'format' => 'raw',
                                    'attribute' => 'operational_status',
                                    'filter' => \false,
                                    'value' => function ($model) {
                                        $name = backend\models\Operationstatus::findOne($model->operational_status)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'Action',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::a("Approve", ["facilities/approve-facility-national", 'id' => $model->id]);
                                    }
                                ]
                            ],
                        ]);
                    }

                    //Rejected district
                    if (!empty($facilityDataProviderRejected) && $facilityDataProviderRejected->count > 0) {
                        echo '<h5>Rejected facilities</h5>
                        <p>Instructions</p>
                        <ol>
                            <li>Below facilities have been rejected.You need to provide more information</li>
                            <li>Click the "Update" link under the Action column to go to the update page</li>
                        </ol>
                        <hr class="dotted short">';

                        echo GridView::widget([
                            'dataProvider' => $facilityDataProviderRejected,
                            'hover' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' => 'name',
                                    'filter' => false,
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'district_id',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\Districts::findOne($model->district_id)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'type',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\Facilitytype::findOne($model->type)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'ownership',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\FacilityOwnership::findOne($model->ownership)->name;
                                        return $name;
                                    },
                                ],
                               [
                                    'format' => 'raw',
                                    'attribute' => 'approver_comments',
                                    'filter' => false,
                                    'label' => "Approval comments",
                                ],
                                [
                                    'attribute' => 'status',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        $str = "";

                                        if ($model->province_approval_status === 2 && $model->national_approval_status == 2) {
                                            $str = "<span class='badge badge-pill badge-danger'> "
                                                    . "<i class='fas fa-times'></i> Rejected at national level,need more infor!<br> See approval comments";
                                        }
                                        if ($model->province_approval_status === 2 && $model->national_approval_status == 0) {
                                            $str = "<span class='badge badge-pill badge-danger'> "
                                                    . "<i class='fas fa-times'></i> Rejected at province level,need more infor!<br> See approval comments";
                                        }


                                        return $str;
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'Action',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::a("Update", ["facilities/update", 'id' => $model->id]);
                                    }
                                ]
                            ],
                        ]);
                    }

                    //Rejected province
                    if (!empty($facilityDataProviderRejectedProvince) && $facilityDataProviderRejectedProvince->count > 0) {
                        echo '<h5>Rejected facilities</h5>
                        <p>Instructions</p>
                        <ol>
                            <li>Below facilities have been rejected.You need to provide more information</li>
                            <li>Click the "Update" link under the Action column to go to the update page</li>
                        </ol>
                        <hr class="dotted short">';

                        echo GridView::widget([
                            'dataProvider' => $facilityDataProviderRejectedProvince,
                            'hover' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' => 'name',
                                    'filter' => false,
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'district_id',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\Districts::findOne($model->district_id)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'type',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\Facilitytype::findOne($model->type)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'attribute' => 'ownership',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $name = backend\models\FacilityOwnership::findOne($model->ownership)->name;
                                        return $name;
                                    },
                                ],
                                [
                                    'format' => 'raw',
                                    'attribute' => 'approver_comments',
                                    'filter' => false,
                                    'label' => "Approval comments",
                                ],
                                [
                                    'attribute' => 'status',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        $str = "";

                                        if ($model->province_approval_status === 2 && $model->national_approval_status == 2) {
                                            $str = "<span class='badge badge-pill badge-danger'> "
                                                    . "<i class='fas fa-times'></i> Rejected at national level,need more infor!<br> See approval comments";
                                        }
                                        if ($model->province_approval_status === 2 && $model->national_approval_status == 0) {
                                            $str = "<span class='badge badge-pill badge-danger'> "
                                                    . "<i class='fas fa-times'></i> Rejected at province level,need more infor!<br> See approval comments";
                                        }


                                        return $str;
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'Action',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::a("Update", ["facilities/update", 'id' => $model->id]);
                                    }
                                ]
                            ],
                        ]);
                    }
                } else {
                    echo "You currently have no tasks";
                }
                ?>
            </div>
        </div>

    </div>

</div>

