<?php
    
    if(isset($quiz)){
        $range = $quiz['cs_start_sec'].",".$quiz['cs_end_sec'];
        $active = ($i == 0);
    }else{
        $range = "10,10";
        $quiz = array(
            'idx' => 0,
            'cs_on' => 0
        );
        $playSec = $_POST['playSec'];
        $i = $_POST['no'];
        $active = true;
    }

?>

<div class="tab-pane <?=$active?'active':''?>" id="q<?=$i+1?>">
    <div class="row addSupriseQuiz">
        <input type="hidden" class="quizIdx" name="quizIdx[]" value=<?=$quiz['idx']?>>
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group">
                    <label class="form-label">Question</label>
                    <div class="input-with-icon right">
                        <i class=""></i>
                        <textarea name="cs_question[]" rows="3" style="width:100%;resize: none;"><?=$quiz['cs_question']?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="controls items">
                    <?php
                        for($j=1 ; $j <= 4; $j++){
                            echo "<div class='answer-group'>";
                            echo "<label>".$j.".</label>";
                            echo "<input name='cs_item_".$j."[]' type='text'  class='form-control' placeholder='' value='".$quiz['cs_item_'.$j]."' >";
                            echo "</div>";
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Answer</label>
                    <select name="cs_answer[]" class="select2 form-control">
                            <?php
                                for($j=1; $j<=4; $j++) {
                                    if((int)$quiz['cs_answer']==$j) $selectedChar = "selected";
                                    else $selectedChar = "";

                                    echo "<option value=\"".$j."\" " . $selectedChar . " >".$j."번</option>";
                                }
                            ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">ON/OFF</label>
                    <div class="checkbox check-success">
                        <input type="hidden" name="cs_on[]" value="<?=$quiz['cs_on']?>">
                        <input type="checkbox" class="cs_on" id="cs_on<?=$i+1?>" <?=($quiz['cs_on']?"checked":"")?>>
                        <label for="cs_on<?=$i+1?>"></label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Time Range</label>
                    <div class="slider slider-horizontal" >
                        <input type="text" data-slider-value="[<?=$range?>]" data-slider-step="1" data-slider-max="<?=$playSec?>" data-slider-min="10" value="<?=$range?>" name="range[]" class="slider-element form-control" data-slider-selection="after">
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-mini btn-danger btnDelQuiz" ><i class="fa fa-minus"> Delete</i></button>
            </div>
        </div>
    </div>
</div>