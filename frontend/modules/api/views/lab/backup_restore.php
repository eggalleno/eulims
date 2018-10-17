

<?php
use yii\helpers\Html;
use yii\bootstrap\Progress;
use kartik\grid\GridView;
use common\models\lab\Sampletype;
use common\models\lab\Services;
use common\models\lab\Lab;
use common\models\lab\Testname;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
use kartik\datetime\DateTimePicker;
use common\components\Functions;
use linslin\yii2\curl;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel common\models\lab\ServicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$func=new Functions();
//echo $func->GetAccessToken(11);




$apiUrl="https://api3.onelab.ph/lab/get-lab?tk=8b5db6ea832b625640122db3e6367b0debca46b4&id=11&rid=11";
$curl = new curl\Curl();
$response = $curl->get($apiUrl);

//$decode=Json::decode($response);
// echo '<pre>';
// print_r($response);
// echo '</pre>';
// echo $response;

// $sampletypelist= ArrayHelper::map(Sampletype::find()->all(),'sampletype_id','type');
// $lablist= ArrayHelper::map(Lab::find()->all(),'lab_id','labname');

$month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$year = ['2013', '2014', '2015', '2016', '2017', '2018'];

//$lablist= ArrayHelper::map( $decode,'lab_id','labname');

$this->title = 'Backup and Restore';
$this->params['breadcrumbs'][] = $this->title;



?>

<?php
       
    ?>

<div class="services-index">
   
<fieldset>
    <legend>Legend/Status</legend>
    <div>
    <span class='badge btn-success legend-font' ><span class= 'glyphicon glyphicon-check'></span> DONE</span>
    <span class='badge btn-danger legend-font' ><span class= 'glyphicon glyphicon-check'></span> PENDING</span>

 
                
    </div>
</fieldset>
   
    <div class="row">
    <div class="image-loader" style="display: hidden;"></div>
    <?php $form = ActiveForm::begin(); ?>
   
        <div>
            <?php 
          echo   $sampletype = "<div class='row'><div class='col-md-2'  style='margin-left:15px'>".$form->field($model,'month')->widget(Select2::classname(),[
                            'data' => $month,
                            'id'=>'month',
                            'theme' => Select2::THEME_KRAJEE,
                            'options' => ['id'=>'month'],
                            'pluginOptions' => ['allowClear' => true,'placeholder' => 'Select Month'],
                    ])."</div>"."<div class='col-md-2'>".$form->field($model,'year')->widget(Select2::classname(),[
                        'data' => $year,
                        'id'=>'year',
                        'theme' => Select2::THEME_KRAJEE,
                        'options' => ['id'=>'year'],
                        'pluginOptions' => ['allowClear' => true,'placeholder' => 'Select Year'],
                ])."</div>"."<div class='col-md-4' style='margin-top:4px'><br><span class='btn btn-success' id='offer' onclick='restore()'>RESTORE</span>"."</div></div>";
            ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    
  
    <div class = "row" style="padding-left:15px;padding-right:15px" id="methodreference">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id'=>'testname-grid',
        'pjax' => true,
      //  'showPageSummary' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-products']],
        'panel' => [
                'type' => GridView::TYPE_PRIMARY,
                'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
               'after'=>false,
            ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],     
            'activity',
            'date',
            'data',
            'month',
            'year',
            [
                'header'=>'Status',
                'hAlign'=>'center',
                'format'=>'raw',
                'value' => function($model) {
                          return "<span class='badge btn-success' style='width:90px;height:20px'>COMPLETED</span>";            
                },
                'enableSorting' => false,
                'contentOptions' => ['style' => 'width:10px; white-space: normal;'],
                // 'pageSummary' => 'Total',
                // 'pageSummary' => true,
                
            ],
        ],
    ]);
    

    ?>
  
</div>

<script type="text/javascript">
    $('#sample-test_id').on('change',function(e) {
       e.preventDefault();
         jQuery.ajax( {
            type: 'GET',
            url: '/lab/services/getmethod?id='+$(this).val(),
            dataType: 'html',
            data: { lab_id: $('#lab_id').val(), sample_type_id: $('#sample-sample_type_id').val()},
            success: function ( response ) {         
              $("#methodreference").html(response);
            },
            error: function ( xhr, ajaxOptions, thrownError ) {
                alert( thrownError );
            }
        });
    });

    function restore(){

        var m = $('#month option:selected').text();
        var y = $('#year option:selected').text();
        
        $.ajax({
            url: "/api/lab/res",
            method: "POST",
            data: {month:m, year:y},
            beforeSend: function(xhr) {

                // var elem = document.getElementById("myBar");
                // var width = 10;
                // var id = setInterval(frame, 10);
                // function frame() {
                //     if (width >= 100) {
                //         clearInterval(id);
                //     } else {
                //         width++;
                //         elem.style.width = width + '%';
                //         elem.innerHTML = width * 1 + '%';
                //     }
               //alert('Pogi!');
                $('.image-loader').addClass("img-loader");
               }
            })
            .done(function( data ) {
                $("#testname-grid").yiiGridView("applyFilter"); 
                $('.image-loader').removeClass("img-loader");
               // showSystemProgress(false); 
            });
  
                        // $.post('/api/lab/res', {
                        //    month: m,
                        //    year: y,
                        // }, function(result){
                        //     $("#testname-grid").yiiGridView("applyFilter");    

                        // });

                        // $.post({
                        //     loader
                        //     xxx
                        //     }).done('tanggaling loader');
                }
</script>


<style type="text/css">
/* Absolute Center Spinner */
.img-loader {
    position: fixed;
    z-index: 999;
    /*height: 2em;
    width: 2em;*/
    height: 64px;
    width: 64px;
    overflow: show;
    margin: auto;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background-image: url('/images/img-loader64.gif');
    background-repeat: no-repeat;
}
/* Transparent Overlay */
.img-loader:before {
    content: '';
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.3);
}
</style>