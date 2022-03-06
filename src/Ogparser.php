<?php
namespace nkwtnb\ogparser;

use Error;

class Ogparser {

  protected $url;
  protected $title;
  protected $description;
  protected $image;

  public function __construct(string $url) {
    $this->url = $url;
  }

  public function fetch() {
    $curl = curl_init($this->url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
    $html = curl_exec($curl);
    $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($status_code !== 200) {
      throw new Error("fetch error");
    }

    $dom = new \DOMDocument();
    $from_encoding = mb_detect_encoding(
      $html,
      [
        'ASCII',
        'UTF-8',
        'EUC-JP',
        'SJIS',
      ],
      true
    );
    if (!$from_encoding) {
      $from_encoding = 'UTF-8';
    }
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $from_encoding));
    $xml_object = simplexml_import_dom($dom);

    $this->title = $this->get_title($xml_object);
    $this->description = $this->get_description($xml_object);
    $this->image = $this->get_thumbnail($xml_object);
    curl_close($curl);
  }

  /**
   * タイトルの取得
   */
  private function get_title($xml_object)
  {
    $value = "";
    $og_tag = $xml_object->xpath('//meta[@property="og:title"]/@content');
    if (count($og_tag) > 0) {
      $value = $og_tag[0];
    } else {
      $title_tag = $xml_object->xpath('//title/text()');
      if (count($title_tag) > 0) {
        $value = $title_tag[0];
      }
    }
    return (string)$value;
  }

  /**
   * 説明の取得
   */
  private function get_description($xml_object)
  {
    $value = "";
    $og_tag = $xml_object->xpath('//meta[@property="og:description"]/@content');
    if (count($og_tag) > 0) {
      $value = $og_tag[0];
    } else {
      $meta_description_tag = $xml_object->xpath('//meta[@name="description"]/@content');
      if (count($meta_description_tag) > 0) {
        $value = $meta_description_tag[0];
      }
    }
    return (string)$value;
  }

  /**
   * サムネイルの取得
   */
  private function get_thumbnail($xml_object)
  {
    $value = "";
    $og_tag = $xml_object->xpath('//meta[@property="og:image"]/@content');
    if (count($og_tag) > 0) {
      $value = $og_tag[0];
    } else {
      $meta_thubnail_tag = $xml_object->xpath('//meta[@name="thumbnail"]/@content');
      if (count($meta_thubnail_tag) > 0) {
        $value = $meta_thubnail_tag[0];
      }
    }
    return (string)$value;
  }

  public static function staTest() {
    
  }
}
