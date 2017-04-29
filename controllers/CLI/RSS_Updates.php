<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class RSS_Updates extends CI_Controller
{
	/**
	 * RSS Updates Controller.
	 * CLI-Cron job to run every 3 hours
	 *
	 */
	
	//Load the needed libraries.  
	public function __construct() 
    {
		parent::__construct();
		// this controller can only be called from the command line
      //  if (!$this->input->is_cli_request()) show_error('Direct access is not allowed');
		$this->load->library('rssparser');
		
		//backup the database
			// Load the DB utility class
			$this->load->model('Database_Manager');			
		//end database backup
	}

	// uses rssparser to load multiple rss feeds and add them to db
	//only one week worth

	public function index()
	{
		
		
		// Get 30 from each. 
		$rss['CBS'] = $this->rssparser->set_feed_url('http://rss.cbssports.com/rss/headlines')->set_cache_life(30)->getFeed(30);
		$rss['ESPN'] = $this->rssparser->set_feed_url('http://sports.espn.go.com/espn/rss/nfl/news')->set_cache_life(30)->getFeed(30);
	
		$rss['Rotoworld'] = $this->rssparser->set_feed_url('http://www.rotoworld.com/rss/feed.aspx?sport=nfl&ftype=news&count=150&format=rss')->set_cache_life(30)->getFeed(150);
		
		$rss['RotoWire'] = $this->rssparser->set_feed_url('http://www.rotowire.com/rss/news.htm?sport=nfl')->set_cache_life(30)->getFeed(10);
		$rss['NFL'] = $this->rssparser->set_feed_url('http://www.nfl.com/rss/rsslanding?searchString=home')->set_cache_life(30)->getFeed(30);
		$rss['FFToday'] = $this->rssparser->set_feed_url('http://www.fftoday.com/rss/news.xml')->set_cache_life(30)->getFeed(30);
		$rss['SI'] = $this->rssparser->set_feed_url('http://www.si.com/rss/si_nfl.rss')->set_cache_life(30)->getFeed(30);
		$rss['SI'] = $this->rssparser->set_feed_url('http://www.si.com/rss/si_fantasy.rss')->set_cache_life(30)->getFeed(30);
		$rss['Fox'] = $this->rssparser->set_feed_url('http://api.foxsports.com/v1/rss?partnerKey=zBaFxRyGKCfxBagJG9b8pqLyndmvo7UU&tag=nfl')->set_cache_life(30)->getFeed(30);
		$rss['NBC-1'] = $this->rssparser->set_feed_url('http://profootballtalk.nbcsports.com/feed/atom/')->set_cache_life(30)->getFeed(30);
		$rss['NBC-2'] = $this->rssparser->set_feed_url('http://profootballtalk.nbcsports.com/category/rumor-mill/feed/atom/')->set_cache_life(30)->getFeed(30);
		$rss['NBC-3'] = $this->rssparser->set_feed_url('http://profootballtalk.nbcsports.com/category/rumor-mill/feed/atom/')->set_cache_life(30)->getFeed(30);

		
		$rss['Yahoo-SEA'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/sea/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-BUF'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/buf/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-MIA'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/mia/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-NE'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/nwe/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-NYJ'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/nyj/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-BAL'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/bal/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-CIN'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/cin/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-CLE'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/cle/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-PIT'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/pit/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-HOU'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/hou/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-IND'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/ind/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-JAX'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/jac/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-TEN'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/ten/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-DEN'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/den/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-KC'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/kan/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-OAK'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/oak/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-SD'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/sdg/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-DAL'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/dal/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-NYG'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/nyg/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-PHI'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/phi/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-WAS'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/was/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-CHI'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/chi/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-DET'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/det/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-GB'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/gnb/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-MIN'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/min/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-ATL'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/atl/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-CAR'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/car/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-NO'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/nor/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-TB'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/tam/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-ARI'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/ari/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-LA'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/lar/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-SF'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/sfo/rss.xml')->set_cache_life(30)->getFeed(30);
		$rss['Yahoo-SEA'] = $this->rssparser->set_feed_url('http://sports.yahoo.com/nfl/teams/sea/rss.xml')->set_cache_life(30)->getFeed(30);
	
		
		foreach ($rss as $source=>$feed)
		{
			foreach ($feed as $item)
			{
				//remove the source tags from the titles
				$item['title']=str_replace('(The Associated Press)','',str_replace('(Reuters)','',str_replace('(Shutdown Corner)','',$item['title'])));
              //check if duplicate
              $duplicates=$this->db->or_where('title',$item['title'])
                		->or_where('description',$item['description'])
						->or_where('link',$item['link'])
                		->from('RSS')
                		->count_all_results();
                
				//if there are 0 duplicates of this item, insert it	
                if($duplicates ==0){
                               
					if($item['pubDate']==''){$pubDate=now();}
					else{ $pubDate=strtotime($item['pubDate']);}
				
                	$this->db->set('title',$item['title'])
						->set('description',$item['description'])
						->set('source',$source)
						->set('date',$pubDate)
						->set('link',$item['link'])
						->insert('RSS');
              }
						

			}
		}
		
		//delete week old stories
				$this->db->where('date <'.(now() - 432000))
						->delete('RSS');
					
		
	}
	
	
}//end Class RSS_Updates 

/*End of file RSS_Updates.php*/
/*Location: ./application/controllers/CLI/RSS_Updates.php*/