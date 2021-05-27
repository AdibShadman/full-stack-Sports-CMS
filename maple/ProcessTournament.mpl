
# ;

# 
restart;
NULL;
# 
NULL;


F := fopen('input_file', 'READ', 'TEXT');
tournament_id := fscanf(F, '%s')[1];


d := fscanf(F, '%s')[1];



NULL;
numGames := fscanf(F, "%d")[1];

Games := [];
Match_ids := [];

for i to numGames do S := fscanf(F, '`%s%s%s`'); Match_ids := [op(Match_ids), S[1]]; P := [convert(S[2], 'name'), convert(S[3], 'name')]; Games := [op(Games), P] end do;
Match_ids;
Games;

NULL;

numPlayers := fscanf(F, "%d")[1];

Standings_Ini := [];
for p to numPlayers do S := fscanf(F, "%s%f%f%s"); player := convert(S[1], 'name'); m := S[2]; s := S[3]; l := S[4]; r := player = Record(mu = m, sigma = s, DP = d, LP = l); Standings_Ini := [op(Standings_Ini), r] end do;

Standings_Ini;

F := fclose(F);


#This formula which updates the players law after stochastic update:

SU:= proc(P::record(mu,sigma,DP,LP))

local

T:=round((Finance:-DayCount(P:-LP, P:-DP)/365)*25 + P:-sigma),

postT:= Record(':-mu'= P:-mu,  ':-sigma'=  T)
;
 postT
end proc:

#Watkins: ?This line is not required
#SU(Record(mu= 1008, sigma= 47, DP= "16/3/2013", LP= "14/4/2012"));
zip(`=`, lhs~(Standings_Ini),SU~(rhs~(Standings_Ini)))
;
Standings:=convert(%, table);

# 
#This formula calculates the new players law after every win or loss:

#Adjustment factors:
AdjFact:= Record(
   ':-Wmu'= .01,    ':-Wsigma'= -40, #winner's factors
   ':-Lmu'= -.02, ':-Lsigma'= -30 #loser's factors
):

PR:= proc(W::record(mu,sigma), L::record(mu,sigma),
          Wid, Lid)
global TAB;
local
   t,
   dist:= evalf@unapply(Statistics:-CDF(Normal(W:-mu, W:-sigma), t), t),
   postW:= Record(
      ':-mu'= W:-mu + AdjFact:-Wmu*W:-mu,
      ':-sigma'= abs(W:-sigma + (AdjFact:-Wsigma*dist(W:-mu)))
   ),
   postL:= Record(
      ':-mu'= L:-mu + AdjFact:-Lmu*L:-mu,
      ':-sigma'= abs(L:-sigma + (AdjFact:-Lsigma*dist(L:-mu)))
   )
;
 TAB[Wid][numwins]:=TAB[Wid][numwins]+1;
 TAB[Wid][wins][TAB[Wid][numwins]]:=[round~([postL:-mu, postL:-sigma, postW:-mu - W:-mu])[], Lid];
 TAB[Lid][numlost]:=TAB[Lid][numlost]+1;
 TAB[Lid][losses][TAB[Lid][numlost]]:=[round~([postW:-mu,  postW:-sigma, postL:-mu - L:-mu])[], Wid];
 userinfo(
      1,  PR,
      sprintf(
        "%a [%d +- %d, point change %+d] defeats %a [%d +- %d, point change %d]", 
         Wid, round~([postW:-mu,  postW:-sigma, postW:-mu - W:-mu])[],
         Lid, round~([postL:-mu, postL:-sigma, postL:-mu - L:-mu])[] #CHANGE IN MEAN
      )
   );    #CHANGED
   postW, postL
end proc:

# 


;
# This applies the formula to calculate the players new laws after each win or loss:

#Watkins:
#   This procdure has some minor modifications to output its results to the output file.
#   Note the change inputs to include 
UpdateAndOutput:= proc(
   Standings::table, 
   Games::list([{name,string}, {name,string}]),
   match_ids::list,
   tournament_id::string,
   tournament_date::string,
   num_matches::integer,
   {inplace::truefalse:= true},
   {showincremental::{And(numeric,positive),NoUserValue}:=':-NoUserValue'}
)
local 
   R:= `if`(inplace, Standings, copy(Standings)),
   G;

#Watkins: Count increments by one each time a game is procdessed
# the only purpose of count is to output the match id to the file.
local count := 1;

#Watkins: Open output file and output intial information
local Out_File := fopen('output_file','WRITE','TEXT');
fprintf(Out_File,"%s\n%s\n%d\n",tournament_id,tournament_date,num_matches);


   for G in Games do
      if assigned(R[G[1]]) and assigned(R[G[2]]) then
         (R[G[1]], R[G[2]]):= PR(R[G[1]], R[G[2]], G[1], G[2])
      else
         error "Player %1 or %2 not found in Standings", G[]
      end if;

      (* Watkins: These statements are not required unless displaying results in maple worksheet
      if showincremental::And(numeric,positive) then
              MakeTable(TAB, R, [op(indets(Games))]);
        Threads:-Sleep(showincremental);
      end if;
      *)

      #output data to file
      fprintf(Out_File,"%s %s %f %f %s %f %f\n", match_ids[count], G[1], R[G[1]][mu], R[G[1]][sigma], G[2], R[G[2]][mu], R[G[2]][sigma]);

      count := count + 1;

   end do;

   #Watkins: close output file
   fclose(Out_File);

   `if`(inplace, [][], eval(R))
end proc:
      
#This makes a dynamic table:

MakeTable:=module()
  export initialize;
  local thetable, i;

  initialize:=proc(Tab)
    unassign(eval(Tab,2));
    

     for i to nops(indets(Games)) do 
      Tab[op(indets(Games))[i]]:=Record(numwins=0,numlost=0,wins=table([]),losses=table([]))
     end do;

    thetable:=Tab;
    NULL:
  end proc;



end module:

# 

infolevel[PR]:= 1:

st := time[real]():

# Initialize the table used as `global` inside `RC` to store results.
MakeTable:-initialize(TAB);

#This call created the new standings for each match. The procedure had been updated to 
#output the raw data to the output file. Note the updated input for the procedure.  
NewStandings:= UpdateAndOutput(Standings, Games, Match_ids, tournament_id, d, numGames, inplace= false):


