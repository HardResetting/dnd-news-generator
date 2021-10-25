## DND-News-Generator

This Webapp is for all the DMs who want to include town-crierlike news. I realized that those might be tedious and hard to come up with. 
Hovever with this template enginge you only have to come up with them once!

If you were to define some Races, Cities and others you might come up with a template like this one:

```text
A [@race_1=[Race]] [@var_1=[ran(0,2)]] other [?[@var_1],Race] 
demand banishment of [@var_3=[ran(100,200)]] [?2,Race] from the town of [City]!
```

You'd recieve a random title for your daily news:

```text
A Elf and 1 other dwarf demand banishment of 147 humans from the town of Voyavik!
```

While there still are some gramatical mistakes it should greatly decrease the work you have to do for your campaign.


## Usage
This is a Laravel-App and has to be hosted on a php-server.
There are ways to host the app on a shared-hosting provider as well. You should be able to find plenty of tutorials for that online.
