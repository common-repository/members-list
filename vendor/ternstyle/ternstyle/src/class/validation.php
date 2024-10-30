<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			validate.php
//		Description:
//			Form post validation.
//		Date:
//			Added on April 15, 2016
//		Version:
//			3.0.1
//		Copyright:
//			Copyright (c) 2021 Ternstyle LLC.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA)
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit: http://www.ternstyle.us/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

namespace MLP\ternstyle;
use MLP\ternstyle\tern_curl as tern_curl;

/****************************************Commence Script*******************************************/

class tern_validation {

	public $e = false;
	public $errors = array();
	private $messages = array(
		'required'	=>		array(
			'message'	=>	'Please fill out all the required fields.',
			'single'	=>	true
		),
		'confirm'	=>		array(
			'message'	=>	'The "%s" fields don\'t match.'
		),
		'nonce'	=>		array(
			'message'	=>	'The "%s" field has been tampered with.'
		),
		'match'		=>		array(
			'message'	=>	'The "%s" fields should not match.'
		),
		'regex'		=>		array(
			'message'	=>	'The "%s" field does not seem to be formatted properly.'
		),
		'swear'		=>		array(
			'message'	=>	'Please do not use potentially offensive words in the "%s" field.'
		),
		'words'		=>		array(
			'message'	=>	'Please shorten the field "%s" to %s words.'
		),
		'chars'		=>		array(
			'message'	=>	'Please shorten the field "%s" to %s characters.'
		),
		'age'		=>		array(
			'message'	=>	'It appears you\'re not "%s" or older.'
		),
		'video-youtube'	=>	array(
			'message'	=>	'The "%s" field does not seem to be a valid YouTube video URL.'
		),
		'security'	=>	array(
			'message'	=>	'Your submission failed security protocol.'
		)
	);
	private $message = array(
		'required'	=>		array(
			'message'	=>	'This field is required.',
			'single'	=>	true
		),
		'confirm'	=>		array(
			'message'	=>	'These fields don\'t match.'
		),
		'nonce'	=>		array(
			'message'	=>	'The field has been tampered with.'
		),
		'match'		=>		array(
			'message'	=>	'These fields should not match.'
		),
		'regex'		=>		array(
			'message'	=>	'This field does not seem to be formatted properly.'
		),
		'swear'		=>		array(
			'message'	=>	'Please do not use potentially offensive words in this.'
		),
		'words'		=>		array(
			'message'	=>	'Please shorten this field to %s words.'
		),
		'chars'		=>		array(
			'message'	=>	'Please shorten this field to %s characters.'
		),
		'age'		=>		array(
			'message'	=>	'It appears you\'re not "%s" or older.'
		),
		'video-youtube'	=>	array(
			'message'	=>	'This field does not seem to be a valid YouTube video URL.'
		),
		'security'	=>	array(
			'message'	=>	'Your submission failed security protocol.'
		)
	);

	public function __construct($f=false,$n=false) {
		$this->fields = $f;
		$this->name = $n;
		$this->session_init();
	}
	public function field_set($f) {
		$this->fields = $f;
	}

/*------------------------------------------------------------------------------------------------
	Session
------------------------------------------------------------------------------------------------*/

	static function session_errors() {
		$x = NULL;
		if(isset($_SESSION['tern_validate'])) {
			$x = $_SESSION['tern_validate'];
			unset($_SESSION['tern_validate']);
		}
		return $x;
	}
	private function session_init() {
		$_SESSION['tern_validate'] = isset($_SESSION['tern_validate']) ? $_SESSION['tern_validate'] : [];
		//$_SESSION['tern_validate'][$this->name] = [];
	}
	private function session_set() {
		$_SESSION['tern_validate'] = isset($_SESSION['tern_validate']) ? $_SESSION['tern_validate'] : [];
		$_SESSION['tern_validate'][$this->name] = $this->errors;
	}
	public function session_get($n='') {
		if(isset($_SESSION['tern_validate'][$n])) {
			$x = $_SESSION['tern_validate'][$n];
			unset($_SESSION['tern_validate'][$n]);
			return $x;
		}
		else {
			return [
				'type'	=>	[],
				'field'	=>	[],
			];
		}
	}

/*------------------------------------------------------------------------------------------------
	Validation
------------------------------------------------------------------------------------------------*/

	public function validate($a=array(),$s=[]) {

		$this->post = $this->post_all = array();
		$this->a = (array)$a;
		$this->nonce = (array)$s;

		$this->e = false;

		foreach($this->fields as $this->k => $this->v) {
			if($this->validate_field()) {
				$this->add_field(true);
			}
			else {
				$this->add_field(false);
			}
		}
		if($this->e) {
			$this->session_set();
			return $this->errors;
		}
		return true;
	}
	public function validate_field() {

		//remove empty array values
		if(isset($this->a[$this->k]) and is_array($this->a[$this->k])) {
			$this->a[$this->k] = array_filter($this->a[$this->k]);
		}
		$this->required();
		$this->recaptcha();
		if(isset($this->a[$this->k])) {
			$this->confirm();
			$this->cant_match();
			$this->regexp();
			$this->swear();
			$this->word_count();
			$this->char_count();
			$this->greater();
			$this->nonce();
			$this->video();
		}
		if($this->e) {
			return false;
		}
		return true;
	}
	private function add_field($valid=false) {

		$value = null;

		//combine values
		if(
			isset($this->a[$this->k]) and
			isset($this->v['keys']) and
			!empty($this->v['keys']) and
			count($this->a[$this->v['keys']]) == count($this->a[$this->k])
		) {
			$value = array_combine((array)$this->a[$this->v['keys']],(array)$this->a[$this->k]);
		}
		elseif(isset($this->a[$this->k])) {
			$value = $this->a[$this->k];

			//sanitize values
			if(isset($this->v['sanitize']) and $this->v['sanitize']) {
				$value = $this->sanitize($value);
			}

		}

		//add values
		$this->post_all[$this->k] = $value;
		if($valid) {
			$this->post[$this->k] = $value;
		}
	}
	private function recaptcha() {
		if(isset($this->v['type']) and $this->v['type'] == 'recaptcha') {

			if(!isset($this->v['secret']) or !isset($this->a['g-recaptcha-response'])) {
				$this->error_add('security',$this->k,$this->v['label'],NULL,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['security']) ? $this->message['security']['message'] : false)));
				$this->e = true;
				return false;
			}

			$c = new tern_curl;
			$r = $c->post([
				'url'	=>	'https://www.google.com/recaptcha/api/siteverify',
				'data'	=>	[
					'secret'		=>	$this->v['secret'],
					'response'	=>	$this->a['g-recaptcha-response'],
					'remoteip'	=>	$this->ip(),
				],
				'options'	=>	array(
					'RETURNTRANSFER'	=>	true,
					'FOLLOWLOCATION'	=>	true,
				),
				'headers'	=>	array(
					'Accept-Charset'	=>	'UTF-8'
				)
			]);
			$r = json_decode($r->body);

			if(!$r->success) {
				$this->error_add('security',$this->k,$this->v['label'],NULL,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['security']) ? $this->message['security']['message'] : false)));
				$this->e = true;
			}

		}
	}
	private function required() {
		if(isset($this->v['required']) and is_array($this->v['required']) and !in_array($this->a['action'],$this->v['required'])) {

		}
		elseif(isset($this->v['required']) and $this->v['required'] and isset($this->v['type']) and $this->v['type'] == 'name') {
			if(!isset($this->a[$this->v['slug-field'].'_first_name']) or empty($this->a[$this->v['slug-field'].'_first_name']) or !isset($this->a[$this->v['slug-field'].'_last_name']) or empty($this->a[$this->v['slug-field'].'_last_name'])) {
				$this->error_add('required',$this->k,$this->v['label'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['required']) ? $this->message['required']['message'] : false)));
				$this->e = true;
			}
		}
		elseif(isset($this->v['required']) and $this->v['required'] and isset($this->v['type']) and $this->v['type'] == 'address') {
			$a = false;
			foreach(array('addy_line_1','addy_city','addy_state','addy_zip') as $v) {
				if(!isset($this->a[$this->v['slug-field'].'_'.$v]) or empty($this->a[$this->v['slug-field'].'_'.$v])) {
					$a = true;
				}
			}
			if($a) {
				$this->error_add('required',$this->k,$this->v['label'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['required']) ? $this->message['required']['message'] : false)));
				$this->e = true;
			}
		}
		elseif(isset($this->v['required']) and $this->v['required'] and (!isset($this->a[$this->k]) or (isset($this->a[$this->k]) and ((isset($this->v['default']) and $this->a[$this->k] == $this->v['default']) or (empty($this->a[$this->k]) and $this->a[$this->k] !== 0 and $this->a[$this->k] !== '0'))))) {
			$this->error_add('required',$this->k,$this->v['label'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['required']) ? $this->message['required']['message'] : false)));
			$this->e = true;
		}
	}
	private function confirm() {
		if(isset($this->v['confirm']) and $this->v['confirm'] and (!isset($this->a[$this->k.'_confirm']) or $this->a[$this->k] !== $this->a[$this->k.'_confirm'])) {
			$this->error_add('confirm',$this->k,$this->v['label'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['confirm']) ? $this->message['confirm']['message'] : false)));
			$this->e = true;
		}
	}
	private function cant_match() {
		if(isset($this->v['match']) and !empty($this->v['match'])) {
			foreach((array)$this->v['match'] as $w) {
				if($this->a[$this->k] === $this->a[$this->fields[$w]['key']]) {
					$this->error_add('match',$this->k,$this->v['label'].' / '.$w,false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['match']) ? $this->message['match']['message'] : false)));
					$this->e = true;
					break;
				}
			}
		}
	}
	private function regexp() {
		if(isset($this->v['regex']) and !empty($this->v['regex'])) {
			if(isset($this->a[$this->k]) and !preg_match($this->v['regex'],$this->a[$this->k])) {
				$this->error_add('regex',$this->k,$this->v['label'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['regex']) ? $this->message['regex']['message'] : false)));
				$this->e = true;
			}
		}
	}
	private function swear() {
		if(isset($this->v['check-swear']) and $this->v['check-swear'] and $this->has_swear($this->a[$this->k])) {
			$this->error_add('swear',$this->k,$this->v['label'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['swear']) ? $this->message['swear']['message'] : false)));
			$this->e = true;
		}
	}
	private function word_count() {
		if(isset($this->v['max-words']) and $this->v['max-words'] > 0 and count(explode(' ',$this->a[$this->k])) > $this->v['max-words']) {
			$this->error_add('words',$this->k,$this->v['label'],$this->v['max-words'],(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['words']) ? $this->message['words']['message'] : false)));
			$this->e = true;
		}
	}
	private function char_count() {
		if(isset($this->v['max-chars']) and $this->v['max-chars'] > 0 and strlen($this->a[$this->k]) > $this->v['max-chars']) {
			$this->error_add('chars',$this->k,$this->v['label'],$this->v['max-chars'],(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['chars']) ? $this->message['chars']['message'] : false)));
			$this->e = true;
		}
	}
	private function greater() {
		if(isset($this->v['min-age']) and !empty($this->v['min-age'])) {
			$t = strtotime($this->a[$this->k]);
			$t = strtotime('+'.$this->v['min-age'].' years',$t);
			if(time() < $t)  {
				$this->error_add('age',$this->k,$this->v['min-age'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['age']) ? $this->message['age']['message'] : false)));
				$this->e = true;
			}
		}
	}
	private function nonce() {
		if(isset($this->v['nonce']) and $this->v['nonce'] and (!isset($this->nonce[$this->k]) or $this->a[$this->k] !== $this->nonce[$this->k])) {
			$this->error_add('nonce',$this->k,$this->v['label'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['nonce']) ? $this->message['nonce']['message'] : false)));
			$this->e = true;
		}
	}

	private $youtube_regex = [
		'/^(https:\/\/www.youtube.com\/watch\?v=)([0-9a-zA-Z_-]{11})$/',
		'/^[0-9a-zA-Z_-]{11}$/',
		'/^(https:\/\/youtu.be\/)([0-9a-zA-Z_-]{11})$/',
		'/^(https:\/\/www.youtube.com\/embed\/)([0-9a-zA-Z_-]{11})$/',
		//'<iframe width="560" height="315" src="https://www.youtube.com/embed/" frameborder="0" allowfullscreen></iframe>'
	];

	private function video() {
		if(isset($this->v['video-youtube']) and $this->v['video-youtube']) {
			$e = true;

			//check if the url is valid
			foreach((array)$this->youtube_regex as $v) {
				if(preg_match($v,$this->a[$this->k],$m)) {
					$e = false;
					break;
				}
			}

			//check if the video exists
			if(!$e and isset($m[2])) {
				$headers = get_headers('http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v='.$m[2].'&format=json');
				if(!preg_match("/(200 OK)/",$headers[0])) {
					$e = true;
				}
			}
			elseif(!$e) {
				$e = true;
			}


			if($e) {
				$this->error_add('video-youtube',$this->k,$this->v['label'],false,(isset($this->v['error']) ? $this->v['error'] : (isset($this->message['video-youtube']) ? $this->message['video-youtube']['message'] : false)));
				$this->e = true;
			}
		}
	}

/*------------------------------------------------------------------------------------------------
	Error Handling
------------------------------------------------------------------------------------------------*/

	private function error_add($t='',$f='',$k=false,$v=false,$m=false) {
		$this->errors['type'][$t][$f] = true;
		$this->errors['field'] = isset($this->errors['field']) ? $this->errors['field'] : array();
		if($k or $v) {
			$this->errors['type'][$t][$f] = array(
				'key'		=>	$k,
				'value'		=>	$v,
				'message'	=>	sprintf($this->messages[$t]['message'],$k)
			);
			$this->errors['field'][$f] = isset($this->errors['field'][$f]) ? $this->errors['field'][$f] : array();
			$this->errors['field'][$f][] = sprintf($m,$k);
		}
	}

	//you can render the errors as one message with $together=true
	public function error_render($together=false) {
		$a = $b = array();
		$m = '';
		if(!empty($this->errors['type'])) {
			foreach((array)$this->errors['type'] as $k => $v) {
				foreach($v as $l => $w) {
					if(isset($a[$k]) and isset($this->messages[$k]['single']) and $this->messages[$k]['single']) {
						continue;
					}
					$a[$k] = $b[] = array('type'=>'danger','message'=>$w['message']);
					$m .= '<p>'.$w['message'].'</p>';
				}
			}
		}

		if($together) {
			return $m;
		}

		return !empty($b) ? $b : NULL;
	}

/*------------------------------------------------------------------------------------------------
	Swear Filtering
------------------------------------------------------------------------------------------------*/

	private $swears = array('ahole','anus','ash0le','ash0les','asholes','ass','Ass Monkey','Assface','assh0le','assh0lez','asshole','assholes','assholz','asswipe','azzhole','bassterds','bastard','bastards','bastardz','basterds','basterdz','Biatch','bitch','bitches','Blow Job','boffing','butthole','buttwipe','c0ck','c0cks','c0k','Carpet Muncher','cawk','cawks','Clit','cnts','cntz','cock','cockhead','cock-head','cocks','CockSucker','cock-sucker','crap','cum','cunt','cunts','cuntz','dick','dild0','dild0s','dildo','dildos','dilld0','dilld0s','dominatricks','dominatrics','dominatrix','dyke','enema','f u c k','f u c k e r','fag','fag1t','faget','fagg1t','faggit','faggot','fagit','fags','fagz','faig','faigs','fart','flipping the bird','fuck','fck','fucker','fuckin','fucking','fucks','Fudge Packer','fuk','Fukah','Fuken','fuker','Fukin','Fukk','Fukkah','Fukken','Fukker','Fukkin','g00k','gayboy','gaygirl','gays','gayz','God-damned','h00r','h0ar','h0re hells','hoar','hoor','hoore','jackoff','jap','japs','jerk-off','jisim','jiss','jizm','jizz','knob','knobs','knobz','kunt','kunts','kuntz','Lesbian','Lezzian','Lipshits','Lipshitz','masochist','masokist','massterbait','masstrbait','masstrbate','masterbaiter','masterbate','masterbates','Motha Fucker','Motha Fuker','Motha Fukkah','Motha Fukker','Mother Fucker','Mother Fukah','Mother Fuker','Mother Fukkah','Mother Fukker','mother-fucker','Mutha Fucker','Mutha Fukah','Mutha Fuker','Mutha Fukkah','Mutha Fukker','n1gr','nastt','nigger;','nigur;','niiger;','niigr;','orafis','orgasim;','orgasm','orgasum','oriface','orifice','orifiss','packi','packie','packy','paki','pakie','paky','pecker','peeenus','peeenusss','peenus','peinus','pen1s','penas','penis','penis-breath','penus','penuus','Phuc','Phuck','Phuk','Phuker','Phukker','polac','polack','polak','Poonani','pr1c','pr1ck','pr1k','pusse','pussee','pussy','puuke','puuker','queer','queers','queerz','qweers','qweerz','qweir','recktum','rectum','retard','sadist','scank','schlong','screwing','semen','sex','sexy','Sh!t','sh1t','sh1ter','sh1ts','sh1tter','sh1tz','shit','shits','shitter','Shitty','Shity','shitz','Shyt','Shyte','Shytty','Shyty','skanck','skank','skankee','skankey','skanks','Skanky','slut','sluts','Slutty','slutz','son-of-a-bitch','tit','turd','va1jina','vag1na','vagiina','vagina','vaj1na','vajina','vullva','vulva','w0p','wh00r','wh0re','whore','xrated','xxx','b!+ch','bitch','blowjob','clit','arschloch','fuck','shit','ass','asshole','b!tch','b17ch','b1tch','bastard','bi+ch','boiolas','buceta','c0ck','cawk','chink','cipa','clits','cock','cum','cunt','dildo','dirsa','ejakulate','fatass','fcuk','fuk','fux0r','hoer','hore','jism','kawk','l3itch','l3i+ch','lesbian','masturbate','masterbat*','masterbat3','motherfucker','s.o.b.','mofo','nigga','nigger','nutsack','phuck','pimpis','pusse','pussy','scrotum','sh!t','shemale','shi+','sh!+','slut','smut','teets','tits','boobs','b00bs','teez','testical','testicle','titt','titties','w00se','jackoff','wank','whoar','whore','*damn','*dyke','*fuck*','*shit*','@$$','amcik','andskota','arse*','assrammer','ayir','bi7ch','bitch*','bollock*','breasts','butt-pirate','cabron','cazzo','chraa','chuj','Cock*','cunt*','d4mn','daygo','dego','dick*','dike*','dupa','dziwka','ejackulate','Ekrem*','Ekto','enculer','faen','fag*','fanculo','fanny','feces','feg','Felcher','ficken','fitt*','Flikker','foreskin','Fotze','Fu(*','fuk*','futkretzn','gook','guiena','h0r','h4x0r','helvete','hoer*','honkey','Huevon','hui','injun','jizz','kanker*','kike','klootzak','kraut','knulle','kuk','kuksuger','Kurac','kurwa','kusi*','kyrpa*','lesbo','mamhoon','masturbat*','merd*','mibun','monkleigh','mouliewop','muie','mulkku','muschi','nepesaurio','nigger*','orospu','paska*','perse','picka','pierdol*','pillu*','pimmel','piss*','pizda','poontsee','poop','porn','p0rn','pr0n','preteen','pula','pule','puta','puto','qahbeh','queef*','rautenberg','schaffer','scheiss*','schlampe','schmuck','screw','sh!t*','sharmuta','sharmute','shipal','shiz','skribz','skurwysyn','sphencter','spic','spierdalaj','splooge','suka','b00b*','testicle*','titt*','twat','vittu','wank*','wetback*','wichser','wop*','yed','zabourah');

	private function has_swear($c='') {

		$l = array();
		$l['a']= '(a|a\.|a\-|4|@|Á|á|À|Â|à|Â|â|Ä|ä|Ã|ã|Å|å|α|Δ|Λ|λ)';
		$l['b']= '(b|b\.|b\-|8|\|3|ß|Β|β)';
		$l['c']= '(c|c\.|c\-|Ç|ç|¢|€|<|\(|{|©)';
		$l['d']= '(d|d\.|d\-|&part;|\|\)|Þ|þ|Ð|ð)';
		$l['e']= '(e|e\.|e\-|3|€|È|è|É|é|Ê|ê|∑)';
		$l['f']= '(f|f\.|f\-|ƒ)';
		$l['g']= '(g|g\.|g\-|6|9)';
		$l['h']= '(h|h\.|h\-|Η)';
		$l['i']= '(i|i\.|i\-|!|\||\]\[|]|1|∫|Ì|Í|Î|Ï|ì|í|î|ï)';
		$l['j']= '(j|j\.|j\-)';
		$l['k']= '(k|k\.|k\-|Κ|κ)';
		$l['l']= '(l|1\.|l\-|!|\||\]\[|]|£|∫|Ì|Í|Î|Ï)';
		$l['m']= '(m|m\.|m\-)';
		$l['n']= '(n|n\.|n\-|η|Ν|Π)';
		$l['o']= '(o|o\.|o\-|0|Ο|ο|Φ|¤|°|ø)';
		$l['p']= '(p|p\.|p\-|ρ|Ρ|¶|þ)';
		$l['q']= '(q|q\.|q\-)';
		$l['r']= '(r|r\.|r\-|®)';
		$l['s']= '(s|s\.|s\-|5|\$|§)';
		$l['t']= '(t|t\.|t\-|Τ|τ)';
		$l['u']= '(u|u\.|u\-|υ|µ)';
		$l['v']= '(v|v\.|v\-|υ|ν)';
		$l['w']= '(w|w\.|w\-|ω|ψ|Ψ)';
		$l['x']= '(x|x\.|x\-|Χ|χ)';
		$l['y']= '(y|y\.|y\-|¥|γ|ÿ|ý|Ÿ|Ý)';
		$l['z']= '(z|z\.|z\-|Ζ)';

		$w = strtolower($c);
		$w = explode(' ',$w);

		foreach((array)$w as $v) {
			$v = trim(preg_replace("/[^a-z]+/",'',preg_replace(array_values($l),array_keys($l),$v)));
			if(in_array($v,$this->swears)) {
				return true;
			}
		}

		return false;

	}

/*------------------------------------------------------------------------------------------------
	Miscellaneous
------------------------------------------------------------------------------------------------*/

	private function sanitize($value) {
		$value = strip_tags($value);
		return $value;
	}
	public function ip() {
		$ip = false;
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip = trim(array_shift($ip));
		}
		elseif(isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

}

/****************************************Terminate Script******************************************/
?>
