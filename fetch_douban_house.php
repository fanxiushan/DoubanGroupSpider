<!DOCTYPE HTML>
<html>
<head>
    	<title>Franky</title>
	<meta charset="utf-8"/>
</head>
<body>
	<a> Hello world </a>
    <?php
    include "simple_html_dom.php";
    $douban_zufang = $_GET['website'];
    $keyword = $_GET['keyword'];
    echo "douban_zufang = ".$douban_zufang."</br>";
    echo "keyword = ".$keyword."</br>";
    if (strlen($douban_zufang) == 0 || strlen($keyword) == 0) {
        return;
    }

    $htmldata = contentOfUrl($douban_zufang);
    echo $htmldata;

    $hrefarray = parseGroupOfHtmlData($htmldata);
    foreach ($hrefarray as $temphref) {
        echo "href:".$temphref."</br>";
    }

    $final_parse_contents = array();
    $i = 0;
    foreach ($hrefarray as $href) {
        //防止IP被封掉.
        // $i = $i + 1;
        // if ($i == 2) {
        //     break;
        // }
        $topic_html_data = contentOfUrl($href);
        $final_parse_contents[$href] = parseTopicOfHtmlData($topic_html_data,$keyword);
    }


    foreach ($final_parse_contents as $key => $value) {
        echo "link:".$key."</br>";
        echo "content:".$value."</br>";
    }


    function contentOfUrl($url) {
        $ch = curl_init($url);
        $ua = 'BlackBerry8700/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/100';
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.cn/');
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    function parseGroupOfHtmlData($htmldata) {
        $html_dom = new DOMDocument();
        @$html_dom->loadHTML($htmldata);
        $hrefarray = array();
        foreach($html_dom->getElementsByTagName("td") as $td_content) {
            # Show the <a href>
            $class = $td_content->getAttribute('class');
            if ($class === "title") {
                foreach ($td_content->getElementsByTagName("a") as $a_content) {
                    $href = $a_content->getAttribute('href');
                    $hrefarray[] = $href;
                }
            }
        }
        return $hrefarray;
    }

    function parseTopicOfHtmlData($htmldata,$t_keyword) {
        $html_dom = new DOMDocument();
        @$html_dom->loadHTML($htmldata);
        foreach ($html_dom->getElementsByTagName("div") as $div_content) {
            $class = $div_content->getAttribute('class');
            if ($class === "topic-content") {
                $pattern = "*".$t_keyword."*";
                echo "</br>"."关键词:".$pattern."</br>";
                preg_match($pattern, $div_content->textContent, $matches);
                // echo $div_content->textContent;
                if (count($matches) > 0) {
                    return $div_content->textContent;
                }
            }
        }
    }
    ?>
</body>
</html>
