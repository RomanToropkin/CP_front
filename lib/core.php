<?php
/**
 * Created by PhpStorm.
 * User: zerox
 * Date: 24.11.18
 * Time: 12:01
 */

function getCURL($url, $post_values = null, $user_headers = null, $method = null) {
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if (isset($post_values) and strlen($post_values) > 1) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_values);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));

        curl_setopt($curl, CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$headers) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $name = strtolower(trim($header[0]));
                if (isset($headers) and !array_key_exists($name, $headers))
                    $headers[$name] = [trim($header[1])];
                else
                    $headers[$name][] = trim($header[1]);

                return $len;
            }
        );

        if (isset($method)) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        if (isset($user_headers)) {

            if (isset($user_headers['User-Agent'])) {
                curl_setopt($curl, CURLOPT_USERAGENT, $user_headers['User-Agent']);
                unset($user_headers['User-Agent']);
            }

            if (isset($user_headers['Cookie'])) {
                curl_setopt($curl, CURLOPT_COOKIE, $user_headers['Cookie']);
                unset($user_headers['Cookie']);
            }

            if (isset($user_headers['Content-Length']))
                unset($user_headers['Content-Length']);

            $user_headers['Content-Length'] = strlen($post_values);

            if (isset($user_headers) and count($user_headers) > 0) {
                $user_headers_req = [];
                foreach ($user_headers as $key => $value)
                    $user_headers_req[] = $key . ": " . $value;
                curl_setopt($curl, CURLOPT_HTTPHEADER, $user_headers_req);
            }
        }

        $out = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        foreach ($headers as $key => $value)
            $headers[$key] = join("; ", $value);
        return ['header' => $headers, 'body' => $out, 'http_code' => $http_code];
    }
}