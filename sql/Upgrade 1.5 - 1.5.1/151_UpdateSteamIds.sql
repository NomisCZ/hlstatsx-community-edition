UPDATE
	hlstats_PlayerUniqueIds
SET
	uniqueId =  replace(uniqueId, 'STEAM_1:',''), uniqueId = replace(uniqueId, 'STEAM_0:','');