<?php

class Message
{
  protected $_user;

  /**
   * Deletes a list of user messages that match their ID.
   *
   * @param array $messages The array of message IDs to delete
   * @param int   $userId   The users ID
   * @return bool If successful
   */
  public function deleteMessages(array $messages, $userId)
  {
    $result = DB::table('message')->where_in('id', $messages)->where('sent_to_id', '=', $userId)->delete();

    if ($result) return true;

    return false;
  }

  public function getMessageWithIdAndEnsureUserOwnsMessage($userId, $messageId)
  {
    $message = DB::table('message')
                  ->where('id', '=', $messageId)
                  ->where('sent_to_id', '=', $userId)
                  ->grab(1)
                  ->get();
    if ($message)
      return $message;

    return false;
  }

  public function countMessages($id)
  {
    $count = DB::table('message')
              ->where('sent_to_id', '=', $id)
              ->where('`read`', '=', 0)
              // ->where('`show_to_receiver`', '=', 1)
              ->count();

    if ($count) return $count->count;

    return 0;
  }

  public function hasInboxMessages($id)
  {
    // Check to see if the user has any messages in their inbix
    $inboxCount = DB::table('message')
                  ->where('sent_to_id', '=', $id)
                  ->where('show_to_receiver', '=', 1)
                  ->count();

    if ($inboxCount)
      return ($inboxCount->count > 0) ? true : false;

    return false;
  }

  public function hasSentMessages($id)
  {
    $sentMessagesCount = DB::table('message')
                         ->where('sent_from_id', '=', $id)
                         ->where('show_to_sender', '=', 1)
                         ->count();

    if ($sentMessagesCount)
      return ($sentMessagesCount->count > 0) ? true : false;

    return false;
  }

  public function hasMessages($id)
  {
    $count = DB::table('message')
        ->where('sent_to_id', '=', $id)
        ->count();

    if (!$count) return false;

    if ($count->count > 0)
      return true;

    return false;
  }

  public function getMessagesSentToId($id)
  {
    $user = $this->_user;
    $messages = DB::table('message')
                ->order_by('id', 'DESC')
                ->where('sent_to_id', '=', $id)
                ->where('show_to_receiver', '=', 1)
                ->get();
    if (!$messages) return array();
    return $messages;
  }

  public function getMessagesSentById($id)
  {
    $messages = DB::table('message')
                ->order_by('id', 'DESC')
                ->where('sent_from_id', '=', $id)
                ->get();

    return $messages;
  }

  public function insertMessageIntoUsersInbox($userId, array $data)
  {
    return DB::table('message')->where('sent_to_id', '=', (int)$userId)->insert($data);
  }

}
