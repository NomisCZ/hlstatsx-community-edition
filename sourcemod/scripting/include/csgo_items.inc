/** Double-include prevention */
#if defined _mynatives_included_
  #endinput
#endif
#define _mynatives_included_
 
/**
 * Get CSGO weapon item name by weapon m_iItemDefinitionIndex.
 * Items name list locate in main plugin Handle:KeyValue structure, which are copied from ...scripts/items/items_game.txt file
 *
 * @param ItemIndex			CSGO weapon m_iItemDefinitionIndex
 * @param ItemName			ItemName buffer.
 * @param ItemName_Size		Maximum length of the ItemName buffer.
 *
 * @noreturn
 */
native char CSGO_GetItemDefinitionNameByIndex(int ItemIndex, char[] ItemName, int ItemName_Size);

/**
 * Get CSGO weapon item index by weapon item name.
 * Items name list locate in main plugin Handle:KeyValue structure, which are copied from ...scripts/items/items_game.txt file
 *
 * @param ItemName	Item name string.
 *
 * @return			Item index (from main plugin Kv).
 */
native int CSGO_GetItemDefinitionIndexByName(const char[] ItemName);