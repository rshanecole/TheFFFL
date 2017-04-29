<?PHP if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * players rules view.
	 *
	 * 
	 */
	//d($this->_ci_cached_vars);
?>
	<script type="text/javascript">
		
		
	</script>
	<div class="panel panel-default" style="margin-top:10px;">
      <div class="panel-heading">
        <h3 class="panel-title text-center"><strong>Available Players</strong></h3>
      </div>
      <div class="panel-body">
      	<h5><strong>Player Restrictions</strong></h5>
        <ul class="list-group">
          <li class="list-group-item"><strong>NFL:</strong> Only NFL players may be used.</li>
          <li class="list-group-item"><strong>Positions:</strong> Each player must be used at the position he plays in the NFL. The position designated by NFL.com will be the player’s official position.</li>
        </ul>
        <h5><strong>Clones</strong></h5>
        <ul class="list-group">
          <li class="list-group-item"><strong>Definition:</strong> Because there will be more than one draft, there is the possibility of some players being acquired by more than one team.  When this happens, the player will have been "cloned."  Players can only be cloned in one of three ways 1) when they are drafted in more than one draft, 2) if two teams acquire a player through free agency, or 3) if a player is designated a franchise player by one team and drafted by one team in the supplemental draft. Despite the existence of "clones," a player may be drafted only once during an individual draft.</li>
          <li class="list-group-item"><strong>Number of Clones Available in Free Agency:</strong> Any time a player is on no roster, that player is considered to have two clones available in free agency. If a player is on one roster, then one clone is available in free agency.</li>
          <li class="list-group-item"><strong>Restrictions:</strong> No team may have on its roster at the same time more than one clone of the same player. Any player whose total number of clones was three will have his first clone that is released into free agency, eliminated.</li>
        </ul>
      </div>
    </div>
    
    <div class="panel panel-default" style="margin-top:10px;">
      <div class="panel-heading">
        <h3 class="panel-title text-center"><strong>Acquiring Players</strong></h3>
      </div>
      <div class="panel-body">
      Players can be acquired by teams in four ways. 1) Through designation as a Franchise Player and thus carried from one season to the next; 2) Through the supplemental and league drafts; 3) Through weekly Free Agency drafts; 4) Through trade with other teams.
      	<h5><strong>Franchise Players</strong></h5>
        <ul class="list-group">
          <li class="list-group-item"><strong>Definition:</strong> Before the drafts, owners will be able to designate players on their roster from the previous year as "franchise" players.  A franchise designation reserves the player for that team for the upcoming season. A player who does not receive the franchise designation will be released by his team at the franchise deadline. A salary cap system will regulate the keeping of franchise players from year to year. Any player designated as a franchise player by two teams will not be available for drafting. </li>
          <li class="list-group-item"><strong>Designation:</strong> Team owners must designate players from their previous roster as franchise players before the franchise deadline the Sunda before the league drafts are scheduled.</li>
          <li class="list-group-item"><strong>Salary Cap:</strong> The salary cap will be $100 million. The total of a team's salaries must fit under the salary cap at the franchise deadline. Once the owner submits his franchise roster and the drafts are completed, the team’s total cap value does not matter for the remainder of the season, i.e. a team can then acquire players with a cap value despite the fact that the team’s total roster would then exceed the $100 million cap. The salary a team must pay a franchise player will be increased each year the team keeps the player on its roster. The player's salary will be increased in the Spring.  The cap value will be calculated as follows:
              <div class="row">
                <div class="col-xs-offset-1 col-xs-22 col-sm-12">
                    <strong>Base Salaries:</strong> The first time a player receives the franchise designation his cap value will be increased by the "base salary" for that position. If the player's salary is less than the base salary, his salary will be increased by his position's base salary. Base salaries are as follows:
                </div>
                <div class="col-xs-offset-1 col-sm-8 col-xs-22">
                  <table class="table table-condensed table-striped table-bordered table-hover" >
                  <caption class="text-center">Base Salaries</caption>
                    <thead>
                        <th>Position</th><th>Base Salary</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>QB/RB/WR</td><td>20 million</td>
                        </tr>
                        <tr>
                            <td>TE</td><td>10 million</td>
                        </tr>
                        <tr>
                            <td>K</td><td>5 million</td>
                        </tr>
                    </tbody>
                  </table>
                 </div>
              </div>
              <div class="row">
                <div class="col-xs-offset-1 col-xs-22 col-sm-12">
                    <strong>Annual Raises:</strong> When a player on the roster that carries a cap value equal to or greater than the base salary is kept the next season, he will receive a raise in salary of
                </div>
                <div class="col-xs-offset-1 col-sm-8 col-xs-22">
                  <table class="table table-condensed table-striped table-bordered table-hover" >
                  <caption class="text-center">Annual Raises</caption>
                    <thead>
                        <th>Position</th><th>Annual Raise</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>QB/RB/WR</td><td>10 million</td>
                        </tr>
                        <tr>
                            <td>TE/K</td><td>5 million</td>
                        </tr>
                        <tr>
                            <td>K</td><td>5 million</td>
                        </tr>
                    </tbody>
                  </table>
                 </div>
              </div>
				The maximum salary for any one player will be $100 million.  If a player carrying a cap value is sent to free agency, the cap value remains attached to that player.
          </li>
          <li class="list-group-item"><strong>Drafting Franchise Players in Supplemental Draft:</strong> If a player is designated as a franchise player by only one team, that player may be drafted in the supplemental draft, but the player will carry with him a salary equal to the salary paid him by the keeping team.The team is not expected to have money left under the salary cap.</li>
           <li class="list-group-item"><strong>Restrictions:</strong> After the franchise deadline, franchise players may not be traded until after the drafts are completed. Any player that carries a salary cap value can be traded at any other time, including prior to the franchise deadline.  However, he still carries that cap value with him.</li>
        </ul>
        <h5><strong>Drafts</strong></h5>
        <ul class="list-group">
          <li class="list-group-item"><strong>Definition:</strong> Before the season starts, players will be acquired by each team through a supplemental draft and league draft. There will be a two round supplemental draft involving all 20 teams in the leauge and two league drafts of ten teams each.  During the draft, team owners will take turns choosing players. When a player has been chosen, he may not be chosen the remainder of that draft.  The draft will last 10 rounds. Only draft picks in the first seven rounds of the league draft are available for trade.</li>
          <li class="list-group-item"><strong>Draft Placement and Draft Order:</strong> Teams will be placed in one of the two drafts through an S-curve method using the previous season’s final standings to alternately place teams in the two drafts.  Those teams that did not qualify for the playoffs will get the first spots in the order.  Of those, order will be determined by overall record with the team with the worst record receiving the first pick.  Those that did make the playoffs will finish out the order in order of playoff advancement.  The team that advanced the furthest in the playoffs will receive the last pick in the draft. Any ties will be broken by overall record with the worst records receiving lower picks.  This order will be the draft order.  Even number rounds will use the draft order in reverse which will be referred to as the reverse draft order. The same method will be used in determining the order of the 20 teams in the supplemental draft. Teams will not necessarily draft with their division or conference rivals. Teams may however be moved from their draft to accommodate each owner’s ability to attend his draft.</li>
          <li class="list-group-item"><strong>Rounds:</strong> The league draft will consist of 10 rounds. Only picks in the first seven rounds are eligible to be traded. All odd rounds will follow the draft order and all even rounds will follow the reverse draft order.</li>
          <li class="list-group-item"><strong>Clock:</strong> Owners will have three minutes to make a selection. If an owner fails to make a selection during the allotted time, the team “passes” and the next team in the draft order may make a selection. At any point the “passed” team may make its selection, but until that time the draft will continue through the draft order.</li>
          <li class="list-group-item"><strong>Positions and Roster Requirements:</strong> Teams are not required to fill all 16 active roster positions by the conclusion of the drafts. Teams may exceed the roster limits during the draft. Rosters must be cut to 16 active players before the final FA draft before the first NFL game of the season. Players may be released after the first free agent draft. The period between the first and last free agent draft before the first NFL game is the only time players may be released from rosters, except when rosters exceed 16 active players after trade, in which case teams are expected to release players as soon as possible.</li>
          <li class="list-group-item"><strong>Supplemental Draft:</strong> The Supplemental Draft will be a two round draft with the first occuring on the Tuesday before the league drafts and the second occuring on the Wednesday following the league drafts. All 20 teams will participate in the supplemental draft. Each team will rank the available players prior to each round of the draft, which will be automated. Players who are designated as franchise players by only one team, will be the only players eligible to be drafted in the first round of the supplemental draft. Any player who is on only one roster will be eligible for drafting in the second round of the supplemental draft. Players drafted in the first round of the supplemental draft will carry with them a salary equal to the salary paid them by the keeping teams. The team is not expected to have money left under the salary cap to draft the player. Once a player is drafted, the player may not be drafted again for the remainder of the supplemental draft. Picks will be assigned in order by draft position using the previous year's standings and playoff finishes in the same method as for the league drafts, with the exception that the winner of the Toilet Bowl will receive the first pick and the runner-up will receive the second pick. The order for the second round will be a reversal of the first round. Supplemental draft picks may not be traded. Any franchise players not drafted in the supplemental draft will become available in the first FA draft.</li>
        </ul>
        <h5><strong>Free Agents</strong></h5>
        <ul class="list-group">
          <li class="list-group-item"><strong>Definition:</strong> Free agents are any players that are not currently on two rosters.  Players may become free agents if they are released by a team, with the exception of the restrictions placed on open free agency, or by any other means discussed in these rules.  All free agents will be available to all teams in the league. </li>
          <li class="list-group-item"><strong>Salary Values of Free Agents:</strong> Any cap value that is attached to a player in free agency will remain with that player. The salary attached to a player in free agency will not be less than the maximum salary being paid that player by any team whose roster he is on. If the player is on one roster, his salary cap value will be equal to the salary being paid him by his owning team. If the player is no longer on a roster after being released, his salary cap value will be unchanged.</li>
          <li class="list-group-item"><strong>Free Agent Draft:</strong> To acquire a free agent, an owner must get him through a Free Agent Draft or during an open free agency period . The drafts will take place twice per week, on Wednesday and Friday. A free agent draft order will be used to determine selections, with teams dropping to the end of the order after making selections.The deadline for entering the first draft is Wednesday evening at 8 p.m. every week.  The deadline for the second is Friday evening at 8 p.m. every week.  Even for weeks during which games are played on Friday or Thursday night, the Friday night draft will still occur.
The first Free Agent Draft will take place on the Friday before the first week of the season. In that draft, teams must release a player or have an open active roster spot in order to draft a player, but may exceed the 16 active roster player limit. After the regular season, there will be only one Free Agent draft. It will take place on Wednesday of week 14. The week 14 Free Agent draft is the final Free Agent draft of the season.
With the exception of the first Free Agent Draft, teams must stay within the 16 player active roster limit in order to draft a free agent. To do so a player must be released or the team must have an open active roster spot.
The drafts will proceed through a special Free Agent Draft Order. Each team, when it is their pick, will have the option of passing or drafting a player. The draft will continue through as many rounds as are needed until all teams have passed.  
The drafts will not be live. To enter a draft, a team owner must simply create and submit his draft list(s) before the deadline.
The draft order will carry over from week to week and from the end of one season to the beginning of the next. When a team makes a selection, the team will move to the end of the order.</li>
          <li class="list-group-item"><strong>Open Free Agency Periods:</strong> After the Friday night free agent draft, teams may immediatley add free agents to their roster. Teams will still move to the bottom of the free agent draft order when they acquire a free agent this way. Only players who were eligible for free agency before the start of the Friday night Free Agent draft and whose available free agents were not drafted and whose scheduled kickoff time has not passed, will be eligible for open free agency.</li>
         
        </ul>
        <h5><strong>Trades</strong></h5>
        <ul class="list-group">
          <li class="list-group-item"><strong>Definition:</strong> Players and/or draft picks may be traded to other teams in the league. Multiple player/pick trades are allowed. Only players on the active roster may be trade</li>
          <li class="list-group-item"><strong>Trading Franchise Players:</strong> Franchise players may not be traded during the period after the Franchise deadline and before the completion of the drafts. Any player that carries a salary cap value can be traded, however, he still carries that cap value with him.</li>
          <li class="list-group-item"><strong>Trading Clones:</strong> Teams may trade clones of the same player. This is allowed only if the palyers' salaries are not the same. For the trade to be approved, the team receiving the player with the lesser salary must give up more than just the player to balance the trade.</li>
          <li class="list-group-item"><strong>Proposing a Trade:</strong> All teams should make their best effort to make all trade offers available to every team in the league and all teams should make their best effort to respond to trade offers made to them by other teams.</li>
          <li class="list-group-item"><strong>Trade Approval:</strong> To maintain the integrity of the league, every trade not accepted during the league drafts, will be reviewed by a trade committee of five members consisting of one member from each of the four divisions and the commissioner. The commissioner alone will approve trades accepted during league drafts. If at least three of the five members believe the trade is unfair to the rest of the league or collusion is involved, the trade will not be allowed. The committee members will have 24 hours from the time the trade is accepted to vote on the trade. After the 24 hours is up, any vote not cast will be an approval vote. All decisions made by the committee are final without appeal. If the trade is accepted less than 24 hours before a player involved in the trade is scheduled to play, an attempt will be made to expedite the process, although approval of the trade is not guaranteed before the player's game. Teams involved in the trade need to make the commissioner aware of any such situation as soon as possible. A member of the committee will not vote on trades involving his team. In those cases if the vote is a 2-2 tie the commissioner will break the tie. In trades involving the commissioner a 2-2 tie will result in denial of the trade. Any trade involving two committee members will be settled by simple majority of the other three members. Trades that the committee might consider unfair or to involve collusion may include but are not limited to trades that involve one team trading considerably better players to another team for considerably lesser players.  The goal is to cut out uneven trades from a weak team to a stronger team so as to make it even stronger or to "sell off" a non contending team to a contending team. To prevent "sell offs" in-season trades should center around player for player exchanges with future season draft picks used only to balance close trades. Also not allowed are trades in which the committee feels that all teams in the league were not given a fair opportunity to be involved in the trade. If a team wishes to trade away a player, all teams should be given equal opportunity to propose an offer for the player or should in the least be made aware that the player is being offered.</li>
          <li class="list-group-item"><strong>Trade Deadline, OTA (Optional Trading Activities), Mini-Camps and Training Camp:</strong> No trades will be allowed after the roster submission deadline for the seventh week of games has passed.  No trade talks or negotiations and no trades may take place after the trade deadline until the OTAs (Optional Trading Activities), Mini-Camp and Training Camp periods. Training Camp will begin one week before the franchise deadline on the Sunday before the franchise deadline. At that point there are no restrictions on trade talks until the league's trade deadline. Mini Camps will begin four days before Training Camps on a Wednesday. During Mini-Camps teams may discuss trades with other teams but may not submit any trades for approval It is recommended that teams not agree to any trades until after Mini-camps, which end when Training Camp begins. The OTA period will begin two days before Mini-Camps on Monday night. During OTAs, trade talks may take place only on the league Facebook page. Trades may not be submitted during OTAs. OTAs will last until Mini-Camps begin.</li>
          <li class="list-group-item"><strong>Restrictions:</strong> If the trade is not finalized before the roster submission deadline for the week, the trade will be cancelled. Any trade may be cancelled or modified if both owners agree to do so before the next Sunday morning roster submission deadline after the trade is originally completed.
Only players on the active roster may be traded.
After the start of the regular season's first NFL game through the trade deadline, no team may acquire by trade any player it traded away until five games have been played and five game weeks have passed.  The five games begin with the roster submission deadline that follows the original trade.
Only draft picks in the first seven rounds of the league drafts may be traded. Supplemental draft picks may not be traded.</li>
         
        </ul>
      </div>
    </div>


</div>



<?PHP
/*End of file login.php*/
/*Location: ./application/veiws/Account/login.php*/