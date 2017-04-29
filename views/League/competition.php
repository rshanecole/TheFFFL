<?PHP if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * rosters rules view.
	 *
	 * 
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
		
		
	</script>
	<div class="panel panel-default" style="margin-top:10px;">
      <div class="panel-heading">
        <h3 class="panel-title text-center"><strong>Games</strong></h3>
      </div>
      <div class="panel-body">
        <ul class="list-group">
          <li class="list-group-item"><strong>Definition:</strong> All rosters will consist of 16 active players with eight players in the starting lineup and an eight player bench. Every week each team will compete head-to-head with another team. Win-loss records will determine standings and berths into the playoffs at the end of the season. A team's starting lineup will be the only players scored.</li>
          <li class="list-group-item"><strong>Tie Breakers:</strong> In the case of a tie in a playoff game, the team with the higher seed (better overall regular season record then points) will advance. In the Super Bowl the tie-breaker will be based on fractional scoring, then most points in all games including playoffs, then most wins, then points in the regular season only, then position performance. In a regular season game, the tie-breaker will be based on fractional scoring then individual position performance. The order of the tie breaker positions is as follows:
              <ol>
                <li>Kicker (team whose kicker scored the most points wins)</li>
                <li>Tight End</li>
                <li>Highest scoring Wide Receiver</li>
                <li>Highest scoring Running Back</li>
                <li>Quarterback</li>
                <li>Second highest scoring Wide Reciever</li>
                <li>Combined Wide Receiver score</li>
                <li>Combined Running Back score</li>
                <li>Team with the higher number of total points at that point in the season</li>
                <li>Team with the better record at that point of the season</li>        
              </ol>
              If, in a regular season game, no winner can be determined by these tie breakers, a single coin toss will determine the winner.
          </li>
          <li class="list-group-item"><strong>Protests/Final Scores:</strong>  Until 10 p.m. the Wednesday following a game any protest may be made regarding the accuracy of the starting lineup or the calculation of the score of the game.  After that deadline, no changes can be made to a starting lineup or the calculation of a players score unless the NFL issues a correction to a player's stats before 6 p.m. on Thursday.</li>
        </ul>
      </div>
    </div>
        
	<div class="panel panel-default" style="margin-top:10px;">
      <div class="panel-heading">
        <h3 class="panel-title text-center"><strong>Scoring</strong></h3>
      </div>
      <div class="panel-body">        
        <ul class="list-group">
          <li class="list-group-item"><strong>Definition:</strong> Each player will accumulate points based on their performance in their NFL game. Only yardage from scrimmage counts.  Kick return yardage does not count although touchdowns scored on kick returns do count.  Negative yardage does count against a player although a player can not have a net score of less than zero.  Rushing and Receiving yardages are scored separately.
          <div class="row">
              <div class="col-xs-offset-1 col-sm-offset-2 col-sm-16 col-xs-22">
                  <table class="table table-condensed table-striped table-bordered table-hover" >
                  	<caption class="text-center">Scoring</caption>
                    <thead>
                        <th>Stat</th><th>Score</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>touchdown passes, rushes, or receptions</td><td>6 pts</td>
                        </tr>
                        <tr>
                            <td>every three completions thrown</td><td>1 pt</td>
                        </tr>
                        <tr>
                            <td>every 25 yds passing</td><td>1 pt</td>
                        </tr>
                        <tr>
                            <td>interception thrown</td><td>-3 pts</td>
                        </tr>
                        <tr>
                            <td>fumble lost</td><td>-2 pts</td>
                        </tr>
                        <tr>
                            <td>every ten yards rushing or receiving</td><td>1 pt</td>
                        </tr>
                        <tr>
                            <td>every two receptions</td><td>1 pt</td>
                        </tr>
                        <tr>
                            <td>every 300 yds passing</td><td>1 bonus pt</td>
                        </tr>
                        <tr>
                            <td>eveary 100 yds rushing or receiving</td><td>1 bonus pt</td>
                        </tr>
                        <tr>
                            <td>two-point conversion (pass, rush, or rec.)</td><td>2 pts</td>
                        </tr>
                        <tr>
                            <td>every PAT</td><td>1 pt</td>
                        </tr>
                        <tr>
                            <td>every fg 0-49 yds</td><td>3 pts</td>
                        </tr>
                        <tr>
                            <td>every fg 50-59 yds</td><td>4 pts</td>
                        </tr>
                        <tr>
                            <td>every fg 60+ yds</td><td>5 pts</td>
                        </tr>
                        <tr>
                            <td>Missed PAT or Missed 0-39 yds</td><td>-1 pt</td>
                        </tr>
                    </tbody>
                  </table>
               </div>
             </div>
          </li>
        </ul>
      </div>
    </div>
    
    <div class="panel panel-default" style="margin-top:10px;">
      <div class="panel-heading">
        <h3 class="panel-title text-center"><strong>Standings Tiebreakers</strong></h3>
      </div>
      <div class="panel-body">
        <ul class="list-group">
          <li class="list-group-item"><strong>Definition:</strong> For any ties that affect championships or standings position, including for determining playoff teams, the following tiebreaker rules will apply. Any tie in total points scored will be broken by overall record. Any tie in record will be broken in the following order. In the case that more than two teams are tied, any team that wins the tie breaker or is still tied with another team to win the tie breaker will move to the next tie breaker. Any team that clearly does not win or tie for the tie breaker will be eliminated. 
             <ol> 
                <li>total points scored</li>
                <li>head-to-head record</li>
                <li>record versus division teams, if all tied teams are in the same division, otherwise skip to next tiebreaker.</li>
                <li>record versus conference teams</li>
                <li>record against the common opponent with the best record. Common opponent must be an opponent that all tied teams have in common.</li>
                <li>second best common opponent, and so on</li>
                <li>coin toss</li>
             </ol>
         </li>
        </ul>
      </div>
    </div>
    
    <div class="panel panel-default" style="margin-top:10px;">
      <div class="panel-heading">
        <h3 class="panel-title text-center"><strong>Postseason</strong></h3>
      </div>
      <div class="panel-body">
        <ul class="list-group">
          <li class="list-group-item"><strong>Playoffs:</strong> Eight teams will participate in the playoffs following the regular season. Each conference will have an equal number of teams in the playoffs. The winners of each division will automatically qualify. The remaining playoff berths will be filled with wildcards; two wildcards will be awarded per conference. The first of the wildcards will be based on overall win-loss record. The final wildcard in each conference will be based on total number of points scored during the regular season. Seeding of those playoff teams will be based on overall record. During the playoffs, no trades will be allowed and only one FA draft will take place on the Wednesday following the conclusion of the last week of the regular season.</li>
          <li class="list-group-item"><strong>Toilet Bowl:</strong> The 12 teams that do not qualify for the playoffs will compete in the Toilet Bowl. The winner of the Toilet Bowl will receive the first pick in the following season's Supplemental Draft and the runner-up will receive the second pick. The Toilet Bowl will consist of two rounds. The first will be a preliminary round in which two highest scoring teams will advance to the Toilet Bowl. The preliminary round will take place during the Divisional Playoff round and the Toilet Bowl will take place during the Conference Championships. Ties will be broken by the same method discussed above for regular season games.
          </li>
          <li class="list-group-item"><strong>Pro Bowl:</strong>  The Pro Bowl will take place during the NFL's week 17. Each team will attempt to put together the highest scoring team possible in week 17. Teams may choose any players, not restricted to those on their own roster.</li>
        </ul>
      </div>
    </div>


</div>



<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/