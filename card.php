<style>
<?php 
	$spymaster = (isset($_GET['spymaster'])) ? TRUE : FALSE;
	if ($spymaster) {
?>
	.red {
		background:red;
	}
	
	.blue {
		background:blue;
	}
	
	.assassin {
		background:black;
	}
	
	.neutral {
		background:green;
	}
<?php } else { ?>
	.red,
	.blue,
	.assassin,
	.neutral {
		background:gray;
	}
	
	.red.touched {
		background:red;
	}
	
	.blue.touched {
		background:blue;
	}
	
	.assassin.touched {
		background:black;
	}
	
	.neutral.touched {
		background:green;
	}	
<?php } ?>
</style>


<style>
	div.card {
		position:relative;
		height:18%;
		width:19%;
		float:left;
		margin:2px;
		border-radius:5px;
		border:2px solid black;
        color:white;
        font-size:200%;
        
        text-shadow:
        -1px -1px 0 #000,  
        1px -1px 0 #000,
        -1px 1px 0 #000,
        1px 1px 0 #000;
        
	}
    
    .rainbow {
        background: linear-gradient(-55deg,
            #ffeeb0 30%, 
            #a2d49f 30%,
            #a2d49f 40%,
            #c7c12f 40%,
            #c7c12f 50%,
            #f26247 50%,
            #f26247 60%,
            #ec2045 60%,
            #ec2045 70%,
            #ffeeb0 70%
        );
    }
    
    span.text {
        position:absolute;
        margin-top:33%;
    }
    
    div.hud {
        height:3%;
        width:3%;
    }
    #hud {
        position:relative;
    }
    
	.hud.blue_hud{
		background:blue;
	}
	.hud.red_hud{
		background:red;
	}
	
    span.hud {
		border-radius:5px;
		margin:0 5px;
        width:32%;
        position:relative;
		float:left;
        background:grey;
        border:solid black 1px;
		text-align:center;
    }
	.big_x {
		position:relative;
		height:18%;
		width:19%;
		float:left;
		margin:2px;
		border-radius:5px;
		border:2px solid black;
        color:red;
		background:#44DA31;
        font-size:800%;
	}
	.big_x span {
		opacity:.5;
		margin-left:25%;
		position:relative;
		top:10%;
	}
</style>
<?php

function build_hud_mouseover() {
    $html = '';
    $teams = array('rainbow', 'blue', 'red');
    foreach($teams as $t) {
        $html .= "<div class='card hud $t'></div>";
    }
    return $html;
}

function show($card) {
    $html = '';
	foreach($card as $k => $v) {
        extract($v);
		$html .= "<div class='card $type'><span class='text'>$word</span></div>";
		if (($k + 1) % 5 == 0) {
			$html .= "<br />\n";
		}
	}
    return $html;
}

function set_words(&$card) {
	$wordlist = file('wordlist.txt', FILE_IGNORE_NEW_LINES);
	shuffle($wordlist);
    foreach ($card as $k => $v) {
        $card[$k] = array('type' => $v, 'word' => $wordlist[$k]);
    }
    // echo '<pre>';
    // print_r($card);
    // echo '</pre>'; 
}

function set_colors(&$card, $counts) {
	$counts['neutral'] = 25 - array_sum($counts);
	$ptr = 0;
	foreach ($counts as $type => $num) {
		for($i = 0; $i < $num; $i++) {
			$card[$ptr] = $type;
			$ptr += 1;
		}
	}
	shuffle($card);
}

function init() {
	$seed = intval($_GET['seed']);
	if (!empty($seed)) {
		srand($seed);
		//echo 'Seeded with: '.$seed.'<br/><br/>';
	}

	$card = range(0, 24);
	$card = array_fill(0, count($card), 0);

	$counts['red'] = $counts['blue'] = 8;
	$counts['assassin'] = 1;
	$first = (rand(1, 2) == 1)? 'red' : 'blue';
	$counts[$first]++;
	set_colors($card, $counts);
	set_words($card);
	return $card;
}
//$hud = build_hud_mouseover();
$card = init();
$main = show($card);
?>
<div id='hud'>
    <span class="hud red_hud">Red</span>
    <span class="hud end_hud">Final</span>
    <span class="hud blue_hud">Blue</span>
</div>
<!--br/-->
<div id='main'>
    <?php echo $main; ?>
</div>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script>
	function log_color( color ) {
		num = 0;
		$('div.'+color).each(function() {
			if (!$(this).hasClass('touched')) {
				num += 1;
			}
		});
		console.log(num);
		$('.hud.'+color+'_hud').text(num+' remaining');
	}

	$( document ).ready(function() {
		log_color('red');
		log_color('blue');
		
		$('.card').click(function() {
			<?php if ($spymaster) { ?>
				opacity = $(this).css('opacity');
				if (opacity != 1) {
					$(this).css('opacity', 1);
				} else {
					$(this).css('opacity', 0.35);
				}
			<?php } else { ?>
				$(this).addClass('touched');
				if ($(this).hasClass('assassin')) {
					$('.hud.end_hud').text('YOU LOSE');
				} else {
					log_color('red');
					log_color('blue');
				}
			<?php } ?>
		});
	});
	//"<div class='big_x'><span>X</span></div>"
</script>
