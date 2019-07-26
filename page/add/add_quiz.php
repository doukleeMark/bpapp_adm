<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "include/config/config.inc");

    include_once(CLASS_PATH . "/quiz.class.lib");
    $quizClass = new quizClass();

    if(isset($_POST['idx']) && $_POST['idx'] > 0){
        $quizRes = $quizClass->getQuizBankInfo($_POST['idx']);
    }
?>

<div class="row">
    <div class="col-xs-12">
        <div class="form-group">
            <label class="form-label">Question</label>
            <div class="input-with-icon right">
                <i class=""></i>
                <textarea name="cq_question" id="cq_question" rows="3" style="width:100%;resize: none;"><?=$quizRes['cq_question']?></textarea>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="controls items">
            <?php
                for($i=1 ; $i <= 4; $i++){
                    echo "<div class='answer-group'>";
                    echo "<label>$i.</label>";
                    echo "<input name='cq_item_$i' type='text'  class='form-control' placeholder='' value='".$quizRes['cq_item_'.$i]."' >";
                    echo "</div>";
                }
            ?>
        </div>
    </div>
</div>
<div class='row'>
    <div class="col-xs-6">
        <div class="form-group">
            <label class="form-label">Answer</label>
            <select name="cq_answer" id="cq_answer" class="select2 form-control">
                    <?php
                        for($i=1; $i<=4; $i++) {
                            if((int)$quizRes['cq_answer']==$i) $selectedChar = "selected";
                            else $selectedChar = "";

                            echo "<option value=\"".$i."\" " . $selectedChar . " >".$i."번</option>";
                        }
                    ?>
            </select>
        </div>
    </div>
</div>
<div class='row'>
    <div class="pull-right">
        <?php
            if(isset($_POST['idx']) && $_POST['idx'] > 0){
                echo "<button type='button' class='btn btn-warning btn-add-update' ><i class='fa'> Update Test</i></button>";
            }else{
                echo "<button type='button' class='btn btn-success btn-add-update' ><i class='fa fa-plus'> Add Test</i></button>";
            }
        ?>
    </div>
</div>