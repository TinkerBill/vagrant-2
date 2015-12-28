
// v6.79	22/9/15	Added outlaw_key to Tweets widget.

jQuery(document).ready(function($) {
	
	if(twitter_params.method == 'outlaw') {
		//twitterFetcher.fetch('349478108283408385', 'twitter_fetcher', twitter_params.count, true); // v6.78 changing Tweets widget so that either script can be used
		twitterFetcher.fetch(twitter_params.outlaw_key, 'twitter_fetcher', twitter_params.count, true);  //v6.79 - so don't have to hardwire for Carplus!
	}
	if(twitter_params.method == 'approved') {
	
	//alert(twitter_params.username);
		$("#tweets .tweet_wrapper").tweet({ // v3.35 was #tweets div
       // avatar_size: 32,
       count: twitter_params.count,
       username: twitter_params.username,
       loading_text: "searching twitter..."
       // refresh_interval: 60
      });
		
	}
		
});
