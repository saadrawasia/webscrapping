<?php


namespace App\Http\Controllers;

use App\Scrap;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Symfony\Component\DomCrawler\Crawler;

use App\Http\Requests;
use SimpleXMLElement;

class ScrapController extends Controller
{
    public function index(){

        $client= new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://chatbotsmagazine.com',
            // You can set any number of default request options.
            //'timeout'  => 2.0,
        ]);
        //$client->setDefaultOption('verify', false);
        $request= $client->request('GET', '/latest', []);
       // $request = new \GuzzleHttp\Psr7\Request('GET', 'https://chatbotsmagazine.com/latest');

        //$res=$client->request('GET','https://chatbotsmagazine.com/latest');
        if($request->getStatusCode() == 200)
        {
            if($request->hasHeader('content-length')){
                $content_length=$request->getHeader('content-length')[0];
            }
            $body=$request->getBody();
            $stringbody=$body->getContents();
            $crawler= new Crawler($stringbody);
            $filter=$crawler->filter('div.js-trackedPost');
            $result=array();
            //dd($crawler->filter('div.section-inner')->children()->getNode(2)->nodeName);
            if (iterator_count($filter) > 1) {

                // iterate over filter results
                foreach ($filter as $i => $content) {

                    // create crawler instance for result
                    $crawler = new Crawler($content);

                  if(strpos($crawler->filter('div.postMetaInline-feedSummary>span>a')->text(),'ago') !== false)
                  {
                      $days=explode(' ',$crawler->filter('div.postMetaInline-feedSummary>span>a')->text());
                      $time=strtotime("- $days[0] day");
                      $date=date('M d',$time);
                  }
                    else{
                        $date=$crawler->filter('div.postMetaInline-feedSummary>span>a')->text();
                    }
                    //dd($crawler->filter('article.postArticle  >a')->attr('href'));
                    // extract the values needed
                     $result[$i] = array(
                            'name' => $crawler->filter('div.postMetaInline-feedSummary>a')->text(),
                            'date' => $date,
                            'title' => $crawler->filter('div.section-inner>h3')->text(),
                            'link'=>$crawler->filter('article.postArticle  >a')->attr('href'),
                            'subtitle' => !is_null($crawler->filter('div.section-inner>h4')->getNode(0)) ? $crawler->filter('div.section-inner>h4')->text() : '',
                            'image'=>!is_null($crawler->filter('figure.graf--figure>div.aspectRatioPlaceholder>img')->getNode(0)) ? $crawler->filter('figure.graf--figure>div.aspectRatioPlaceholder>img')->attr('src'):'',
                            //'image'=>$crawler->filter('div.section-inner>img.progressiveMedia-image'),
                        );

                }
            }

        }

            for($j=0; $j<count($result); $j++){
                $scrap=Scrap::firstOrCreate([
                    'name'=>$result[$j]['name'],
                    'title'=>$result[$j]['title'],
                    'subtitle'=>$result[$j]['subtitle'],
                    'date'=>$result[$j]['date'],
                    'img'=>$result[$j]['image'],
                    'link'=>$result[$j]['link']
                ]);

            }

        $scrapped_data = Scrap::orderBy('id','ASC')->get();
        return view('welcome',compact('scrapped_data'));
    }

    public function show(){
        //dd(Input::get('abc'));
        $client= new Client(/*[
            // Base URI is used with relative requests
            'base_uri' => Input::get('abc'),
            // You can set any number of default request options.
            //'timeout'  => 2.0,
        ]*/);
        //$client->setDefaultOption('verify', false);
        $request= $client->request('GET', Input::get('abc'), []);
        // $request = new \GuzzleHttp\Psr7\Request('GET', 'https://chatbotsmagazine.com/latest');

        //$res=$client->request('GET','https://chatbotsmagazine.com/latest');
        if($request->getStatusCode() == 200)
        {
            if($request->hasHeader('content-length')){
                $content_length=$request->getHeader('content-length')[0];
            }
            $body=$request->getBody();
            $stringbody=$body->getContents();
            $crawler= new Crawler($stringbody);
            $filter=$crawler->filter('article.postArticle');
            $result=array();
            //dd(iterator_count($filter));
            //dd($crawler->filter('div.section-inner')->children()->getNode(2)->nodeName);
            if (iterator_count($filter) >= 1) {

                // iterate over filter results
                foreach ($filter as $i => $content) {

                    // create crawler instance for result
                    $crawler = new Crawler($content);

                    if(strpos($crawler->filter('span.postMetaInline')->text(),'ago') !== false)
                    {
                        $days=explode(' ',$crawler->filter('span.postMetaInline')->text());
                        $time=strtotime("- $days[0] day");
                        $date=date('M d',$time);
                    }
                    else{
                        $days=$crawler->filter('span.postMetaInline>span')->eq(1)->text();
                        $date=explode($days,$crawler->filter('span.postMetaInline')->text());
                        $date=$date[0];
                       // dd($date);
                    }
                   // dd($crawler->filter('span.postMetaInline>span')->eq(1)->text());
                    $content='';
                    $item=$crawler->filter('main.postArticle-content>section>div.section-content>div.section-inner>p');
                    foreach( $item as $item ){
                        $content.=$item->textContent;
                    }
                    //dd($crawler->filter('span.postMetaInline')->text());


                    // extract the values needed
                    $result[$i] = array(
                        'name' => $crawler->filter('div.postMetaLockup>div.postMetaInline-feedSummary >a')->text(),
                        'date' => $date,
                        'title' => $crawler->filter('div.section-inner>h3')->text(),
                        'subtitle' => !is_null($crawler->filter('div.section-inner>h4')->getNode(0)) ? $crawler->filter('div.section-inner>h4')->text() : '',
                        //'image'=>!is_null($crawler->filter('figure.graf--figure>div.aspectRatioPlaceholder>img')->getNode(0)) ? $crawler->filter('figure.graf--figure>div.aspectRatioPlaceholder>img')->attr('src'):'',
                        //'image'=>$crawler->filter('div.section-inner>img.progressiveMedia-image'),
                        'content'=>$content
                    );

                }
            }

        }

        return view('show',compact('result'));

    }
}
