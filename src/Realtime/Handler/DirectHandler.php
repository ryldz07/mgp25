<?php

namespace InstagramAPI\Realtime\Handler;

use InstagramAPI\Client as HttpClient;
use InstagramAPI\Realtime\HandlerInterface;
use InstagramAPI\Realtime\Message;
use InstagramAPI\Realtime\Payload\Action\AckAction;
use InstagramAPI\Realtime\Payload\Event\PatchEvent;
use InstagramAPI\Realtime\Payload\Event\PatchEventOp;
use InstagramAPI\Realtime\Payload\RealtimeAction;
use InstagramAPI\Realtime\Payload\RealtimeEvent;
use InstagramAPI\Realtime\Payload\StoryScreenshot;
use InstagramAPI\Realtime\Payload\ThreadAction;
use InstagramAPI\Realtime\Payload\ThreadActivity;
use InstagramAPI\Response\Model\ActionBadge;
use InstagramAPI\Response\Model\DirectInbox;
use InstagramAPI\Response\Model\DirectSeenItemPayload;
use InstagramAPI\Response\Model\DirectThread;
use InstagramAPI\Response\Model\DirectThreadItem;
use InstagramAPI\Response\Model\DirectThreadLastSeenAt;

class DirectHandler extends AbstractHandler implements HandlerInterface
{
    const MODULE = 'direct';

    const THREAD_REGEXP = '#^/direct_v2/inbox/threads/(?<thread_id>[^/]+)$#D';
    const ITEM_REGEXP = '#^/direct_v2/threads/(?<thread_id>[^/]+)/items/(?<item_id>[^/]+)$#D';
    const ACTIVITY_REGEXP = '#^/direct_v2/threads/(?<thread_id>[^/]+)/activity_indicator_id/(?<context>[^/]+)$#D';
    const STORY_REGEXP = '#^/direct_v2/visual_threads/(?<thread_id>[^/]+)/items/(?<item_id>[^/]+)$#D';
    const SEEN_REGEXP = '#^/direct_v2/threads/(?<thread_id>[^/]+)/participants/(?<user_id>[^/]+)/has_seen$#D';
    const SCREENSHOT_REGEXP = '#^/direct_v2/visual_thread/(?<thread_id>[^/]+)/screenshot$#D';
    const BADGE_REGEXP = '#^/direct_v2/visual_action_badge/(?<thread_id>[^/]+)$#D';

    /** {@inheritdoc} */
    public function handleMessage(
        Message $message)
    {
        $data = $message->getData();

        if (isset($data['event'])) {
            $this->_processEvent($data);
        } elseif (isset($data['action'])) {
            $this->_processAction($data);
        } else {
            throw new HandlerException('Invalid message (both event and action are missing).');
        }
    }

    /**
     * Process incoming event.
     *
     * @param array $message
     *
     * @throws HandlerException
     */
    protected function _processEvent(
        array $message)
    {
        if ($message['event'] === RealtimeEvent::PATCH) {
            $event = new PatchEvent($message);
            foreach ($event->getData() as $op) {
                $this->_handlePatchOp($op);
            }
        } else {
            throw new HandlerException(sprintf('Unknown event type "%s".', $message['event']));
        }
    }

    /**
     * Process incoming action.
     *
     * @param array $message
     *
     * @throws HandlerException
     */
    protected function _processAction(
        array $message)
    {
        if ($message['action'] === RealtimeAction::ACK) {
            $this->_target->emit('client-context-ack', [new AckAction($message)]);
        } else {
            throw new HandlerException(sprintf('Unknown action type "%s".', $message['action']));
        }
    }

    /**
     * Patch op handler.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _handlePatchOp(
        PatchEventOp $op)
    {
        switch ($op->getOp()) {
            case PatchEventOp::ADD:
                $this->_handleAdd($op);
                break;
            case PatchEventOp::REPLACE:
                $this->_handleReplace($op);
                break;
            case PatchEventOp::REMOVE:
                $this->_handleRemove($op);
                break;
            case PatchEventOp::NOTIFY:
                $this->_handleNotify($op);
                break;
            default:
                throw new HandlerException(sprintf('Unknown patch op "%s".', $op->getOp()));
        }
    }

    /**
     * Handler for the ADD op.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _handleAdd(
        PatchEventOp $op)
    {
        $path = $op->getPath();
        if ($this->_pathStartsWith($path, '/direct_v2/threads')) {
            if (strpos($path, 'activity_indicator_id') === false) {
                $this->_upsertThreadItem($op, true);
            } else {
                $this->_updateThreadActivity($op);
            }
        } elseif ($this->_pathStartsWith($path, '/direct_v2/inbox/threads')) {
            $this->_upsertThread($op, true);
        } elseif ($this->_pathStartsWith($path, '/direct_v2/visual_threads')) {
            $this->_updateDirectStory($op);
        } else {
            throw new HandlerException(sprintf('Unsupported ADD path "%s".', $path));
        }
    }

    /**
     * Handler for the REPLACE op.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _handleReplace(
       PatchEventOp $op)
    {
        $path = $op->getPath();
        if ($this->_pathStartsWith($path, '/direct_v2/threads')) {
            if ($this->_pathEndsWith($path, 'has_seen')) {
                $this->_updateSeen($op);
            } else {
                $this->_upsertThreadItem($op, false);
            }
        } elseif ($this->_pathStartsWith($path, '/direct_v2/inbox/threads')) {
            $this->_upsertThread($op, false);
        } elseif ($this->_pathEndsWith($path, 'unseen_count')) {
            if ($this->_pathStartsWith($path, '/direct_v2/inbox')) {
                $this->_updateUnseenCount('inbox', $op);
            } else {
                $this->_updateUnseenCount('visual_inbox', $op);
            }
        } elseif ($this->_pathStartsWith($path, '/direct_v2/visual_action_badge')) {
            $this->_directStoryAction($op);
        } elseif ($this->_pathStartsWith($path, '/direct_v2/visual_thread')) {
            if ($this->_pathEndsWith($path, 'screenshot')) {
                $this->_notifyDirectStoryScreenshot($op);
            } else {
                $this->_createDirectStory($op);
            }
        } else {
            throw new HandlerException(sprintf('Unsupported REPLACE path "%s".', $path));
        }
    }

    /**
     * Handler for the REMOVE op.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _handleRemove(
        PatchEventOp $op)
    {
        $path = $op->getPath();
        if ($this->_pathStartsWith($path, '/direct_v2')) {
            $this->_removeThreadItem($op);
        } else {
            throw new HandlerException(sprintf('Unsupported REMOVE path "%s".', $path));
        }
    }

    /**
     * Handler for NOTIFY op.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _handleNotify(
        PatchEventOp $op)
    {
        $path = $op->getPath();
        if ($this->_pathStartsWith($path, '/direct_v2/threads')) {
            $this->_notifyThread($op);
        } else {
            throw new HandlerException(sprintf('Unsupported NOTIFY path "%s".', $path));
        }
    }

    /**
     * Handler for thread creation/modification.
     *
     * @param PatchEventOp $op
     * @param bool         $insert
     *
     * @throws HandlerException
     */
    protected function _upsertThread(
        PatchEventOp $op,
        $insert)
    {
        $event = $insert ? 'thread-created' : 'thread-updated';
        if (!$this->_hasListeners($event)) {
            return;
        }

        if (!preg_match(self::THREAD_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match thread regexp.', $op->getPath()));
        }

        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode thread JSON: %s.', json_last_error_msg()));
        }

        $this->_target->emit($event, [$matches['thread_id'], new DirectThread($json)]);
    }

    /**
     * Handler for thread item creation/modification.
     *
     * @param PatchEventOp $op
     * @param bool         $insert
     *
     * @throws HandlerException
     */
    protected function _upsertThreadItem(
        PatchEventOp $op,
        $insert)
    {
        $event = $insert ? 'thread-item-created' : 'thread-item-updated';
        if (!$this->_hasListeners($event)) {
            return;
        }

        if (!preg_match(self::ITEM_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match thread item regexp.', $op->getPath()));
        }

        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode thread item JSON: %s.', json_last_error_msg()));
        }

        $this->_target->emit($event, [$matches['thread_id'], $matches['item_id'], new DirectThreadItem($json)]);
    }

    /**
     * Handler for thread activity indicator.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _updateThreadActivity(
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('thread-activity')) {
            return;
        }

        if (!preg_match(self::ACTIVITY_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match thread activity regexp.', $op->getPath()));
        }

        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode thread activity JSON: %s.', json_last_error_msg()));
        }

        $this->_target->emit('thread-activity', [$matches['thread_id'], new ThreadActivity($json)]);
    }

    /**
     * Handler for story update.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _updateDirectStory(
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('direct-story-updated')) {
            return;
        }

        if (!preg_match(self::STORY_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match story item regexp.', $op->getPath()));
        }

        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode story item JSON: %s.', json_last_error_msg()));
        }

        $this->_target->emit(
            'direct-story-updated',
            [$matches['thread_id'], $matches['item_id'], new DirectThreadItem($json)]
        );
    }

    /**
     * Handler for unseen count.
     *
     * @param string       $inbox
     * @param PatchEventOp $op
     */
    protected function _updateUnseenCount(
        $inbox,
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('unseen-count-update')) {
            return;
        }

        $payload = new DirectSeenItemPayload([
            'count'     => (int) $op->getValue(),
            'timestamp' => $op->getTs(),
        ]);
        $this->_target->emit('unseen-count-update', [$inbox, $payload]);
    }

    /**
     * Handler for thread seen indicator.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _updateSeen(
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('thread-seen')) {
            return;
        }

        if (!preg_match(self::SEEN_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match thread seen regexp.', $op->getPath()));
        }
        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode thread seen JSON: %s.', json_last_error_msg()));
        }

        $this->_target->emit(
            'thread-seen',
            [$matches['thread_id'], $matches['user_id'], new DirectThreadLastSeenAt($json)]
        );
    }

    /**
     * Handler for screenshot notification.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _notifyDirectStoryScreenshot(
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('direct-story-screenshot')) {
            return;
        }

        if (!preg_match(self::SCREENSHOT_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match thread screenshot regexp.', $op->getPath()));
        }

        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode thread JSON: %s.', json_last_error_msg()));
        }

        $this->_target->emit('direct-story-screenshot', [$matches['thread_id'], new StoryScreenshot($json)]);
    }

    /**
     * Handler for direct story creation.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _createDirectStory(
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('direct-story-created')) {
            return;
        }

        if ($op->getPath() !== '/direct_v2/visual_thread/create') {
            throw new HandlerException(sprintf('Path "%s" does not match story create path.', $op->getPath()));
        }

        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode inbox JSON: %s.', json_last_error_msg()));
        }

        $inbox = new DirectInbox($json);
        $allThreads = $inbox->getThreads();
        if ($allThreads === null || !count($allThreads)) {
            return;
        }
        $this->_target->emit('direct-story-created', [reset($allThreads)]);
    }

    /**
     * Handler for story action.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _directStoryAction(
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('direct-story-action')) {
            return;
        }

        if (!preg_match(self::BADGE_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match story action regexp.', $op->getPath()));
        }

        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode story action JSON: %s.', json_last_error_msg()));
        }

        $this->_target->emit(
            'direct-story-action',
            [$matches['thread_id'], new ActionBadge($json)]
        );
    }

    /**
     * Handler for thread item removal.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _removeThreadItem(
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('thread-item-removed')) {
            return;
        }

        if (!preg_match(self::ITEM_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match thread item regexp.', $op->getPath()));
        }

        $this->_target->emit('thread-item-removed', [$matches['thread_id'], $matches['item_id']]);
    }

    /**
     * Handler for thread notify.
     *
     * @param PatchEventOp $op
     *
     * @throws HandlerException
     */
    protected function _notifyThread(
        PatchEventOp $op)
    {
        if (!$this->_hasListeners('thread-notify')) {
            return;
        }

        if (!preg_match(self::ITEM_REGEXP, $op->getPath(), $matches)) {
            throw new HandlerException(sprintf('Path "%s" does not match thread item regexp.', $op->getPath()));
        }
        $json = HttpClient::api_body_decode($op->getValue());
        if (!is_array($json)) {
            throw new HandlerException(sprintf('Failed to decode thread item notify JSON: %s.', json_last_error_msg()));
        }

        $this->_target->emit(
            'thread-notify',
            [$matches['thread_id'], $matches['item_id'], new ThreadAction($json)]
        );
    }

    /**
     * Checks if the path starts with specified substring.
     *
     * @param string $path
     * @param string $string
     *
     * @return bool
     */
    protected function _pathStartsWith(
        $path,
        $string)
    {
        return strncmp($path, $string, strlen($string)) === 0;
    }

    /**
     * Checks if the path ends with specified substring.
     *
     * @param string $path
     * @param string $string
     *
     * @return bool
     */
    protected function _pathEndsWith(
        $path,
        $string)
    {
        $length = strlen($string);

        return substr_compare($path, $string, strlen($path) - $length, $length) === 0;
    }
}
