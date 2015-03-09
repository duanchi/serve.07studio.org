<?php
/**
 * File    libraries\Function\_curl.func.php
 * Desc    curl 调用函数
 * Manual  svn://svn.vop.com/api/manual/Function/_curl
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-18
 * Time    15:57
 */



function _curl($url, $request = null, $request_type = HTTP_GET, $timeout = 10, $return_header = FALSE, $header = array(), $referer = '', $user_agent = '', $cookie = '', $cookiejar = FALSE, $cookie_file = null) {

	$curl_handler = curl_init ();
	is_array ( $header ) ? curl_setopt ( $curl_handler, CURLOPT_HEADER, $header ) : curl_setopt ( $curl_handler, CURLOPT_HEADER, array ($header ) );
	if (HTTP_POST === $request_type) {
		curl_setopt ( $curl_handler, CURLOPT_URL, $url );
		curl_setopt ( $curl_handler, CURLOPT_POST, 1 );
		curl_setopt ( $curl_handler, CURLOPT_POSTFIELDS, $request );
	} else
		curl_setopt ( $curl_handler, CURLOPT_URL, $url . '?' . $request );
	$user_agent == '' ? curl_setopt ( $curl_handler, CURLOPT_USERAGENT, $user_agent ) : curl_setopt ( $curl_handler, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] );
	$referer ? curl_setopt ( $curl_handler, CURLOPT_REFERER, $referer ) : curl_setopt ( $curl_handler, CURLOPT_AUTOREFERER, 1 );
	if ($cookie) {
		curl_setopt ( $curl_handler, CURLOPT_COOKIE, $cookie );
	}
	if (TRUE === $cookiejar) {
		if ($cookie_file == null) _throw('500');
		curl_setopt ( $curl_handler, CURLOPT_COOKIEJAR, $cookie_file );
		curl_setopt ( $curl_handler, CURLOPT_COOKIEFILE, $cookie_file );
	}
	if (TRUE === $return_header) {
		curl_setopt ( $curl_handler, CURLOPT_HEADER, 1 );
	}
	curl_setopt ( $curl_handler, CURLOPT_TIMEOUT, $timeout );
	curl_setopt ( $curl_handler, CURLOPT_HTTPHEADER, $header );
	curl_setopt ( $curl_handler, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $curl_handler, CURLOPT_FOLLOWLOCATION,1);  //是否抓取跳转后的页面
	try {
		$response = curl_exec ($curl_handler);
		//if (curl_getinfo($curl,CURLINFO_HTTP_CODE) > '299')
		if (curl_errno ($curl_handler)) {
			_throw (curl_errno ($curl_handler));
		}
		$result = ['status_code'=>curl_getinfo($curl_handler,CURLINFO_HTTP_CODE),'response'=>$response];
		curl_close ($curl_handler);
		return $result;
	} catch (Exception$e) {
		_catch ($e);
	}
}
/**
 * @name multi_curl
 * curl并发封装函数
 * @param curl_opt(url,request,header,user_agent,cookie,cookiejar,cookie_file)
 * @param request_type define(GET,POST)
 * @param timeout int
 */
function multi_curl($curl_opt, $request_type = 'GET', $timeout = '10') {
	$multi_handler = curl_multi_init ();
	$thread_number = count ( $curl_opt );
	$i = 0;
	for($i; $i < $thread_number; $i ++) {
		$curl_handler [$i] = curl_init ();
		is_array ( $curl_opt [$i] ['header'] ) ? curl_setopt ( $curl_handler [$i], CURLOPT_HTTPHEADER, $curl_opt [$i] ['header'] ) : FALSE;
		if ('POST' === $request_type) {
			curl_setopt ( $curl_handler [$i], CURLOPT_URL, $curl_opt [$i] ['url'] );
			curl_setopt ( $curl_handler [$i], CURLOPT_POST, 1 );
			curl_setopt ( $curl_handler [$i], CURLOPT_POSTFIELDS, $curl_opt [$i] ['request'] );
		} else
			curl_setopt ( $curl_handler [$i], CURLOPT_URL, $curl_opt [$i] ['url'] . '?' . $curl_opt [$i] ['request'] );
		$curl_opt [$i] ['user_agent'] ? curl_setopt ( $curl_handler [$i], CURLOPT_USERAGENT, $curl_opt [$i] ['user_agent'] ) : curl_setopt ( $curl_handler [$i], CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] );
		$curl_opt [$i] ['referer'] ? curl_setopt ( $curl_handler [$i], CURLOPT_REFERER, $curl_opt [$i] ['referer'] ) : curl_setopt ( $curl_handler [$i], CURLOPT_AUTOREFERER, 1 );
		if ($curl_opt [$i] ['cookie']) {
			curl_setopt ( $curl_handler [$i], CURLOPT_COOKIE, $curl_opt [$i] ['cookie'] );
		}
		if ('COOKIEJAR' === $curl_opt [$i] ['cookiejar']) {
			$curl_opt [$i] ['cookie_file'] = COOKIE_PATH . $curl_opt [$i] ['cookie_file'];
			curl_setopt ( $curl_handler [$i], CURLOPT_COOKIEJAR, $curl_opt [$i] ['cookie_file'] );
			curl_setopt ( $curl_handler [$i], CURLOPT_COOKIEFILE, $curl_opt [$i] ['cookie_file'] );
		}
		curl_setopt ( $curl_handler [$i], CURLOPT_TIMEOUT, $timeout );
		curl_setopt ( $curl_handler [$i], CURLOPT_RETURNTRANSFER, 1 );
		curl_multi_add_handle ( $multi_handler, $curl_handler [$i] );
	}
	try {
		do {
			$exec_handler = curl_multi_exec ( $multi_handler, $active ); //当无数据时或请求暂停时，active=true
		} while ( $exec_handler == CURLM_CALL_MULTI_PERFORM ); //当正在接受数据时
		while ( $active and $exec_handler == CURLM_OK ) { //当无数据时或请求暂停时，active=true,为了减少cpu的无谓负担,这一步很难明啊
			if (curl_multi_select ( $multi_handler ) != - 1) {
				do {
					$exec_handler = curl_multi_exec ( $multi_handler, $active );
					if (! isset ( $exec_handler )) {
						_throw ( '500' );
					}
				} while ( $exec_handler == CURLM_CALL_MULTI_PERFORM );
			}
		}
		$response = [];
		foreach ( $curl_handler as $key => $value ) {
			$response[] = [
				'status_code'	=>	curl_getinfo($value,CURLINFO_HTTP_CODE),
				'response'	=>	curl_multi_getcontent ($value),
			];
			curl_close ( $curl_handler [$key] ); //关闭所有对象
			curl_multi_remove_handle ( $multi_handler, $curl_handler [$key] ); //用完马上释放资源
		}
		curl_multi_close ( $multi_handler );
		unset ( $multi_handler );
		unset ( $curl_handler );
		return $response;
	} catch ( Exception $e ) {
		_catch ( $e );
	}
}