<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-11-9
 * Time: 上午9:52
 */

namespace App\Http\Controllers;
use App\Handlers\BaiduAIPHandler;
use App\Handlers\LogHandler;
use App\Handlers\WechatMessageHandler;
use App\Jobs\ReceiveWechatMessage;

class WechatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        LogHandler::log('wechat', 'serve', request()->all());

        $app = app('wechat.official_account');
        $app->server->push(function($message){
            LogHandler::log('wechat', 'serve-message', $message);
            switch ($message['MsgType']) {
                case 'event':
                    // 点击事件
                    //{"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543482755","MsgType":"event","Event":"CLICK","EventKey":"click_2"}
                    // 普通扫码
                    // {"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543482854","MsgType":"event","Event":"scancode_push","EventKey":"click_4","ScanCodeInfo":{"ScanType":"qrcode","ScanResult":"萨达sda所大"}}
                    // 等待扫码
                    // {"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543482863","MsgType":"event","Event":"scancode_waitmsg","EventKey":"click_5","ScanCodeInfo":{"ScanType":"qrcode","ScanResult":"萨达sda所大"}}
                    // 打开链接
                    // {"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543483275","MsgType":"event","Event":"VIEW","EventKey":"https:\/\/www.klinson.com\/","MenuId":"422344901"}
                    return '收到事件消息';
                    break;
                case 'text':
                    //{"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543482671","MsgType":"text","Content":"999","MsgId":"6629207594310057452"}
                    dispatch(new ReceiveWechatMessage($message));

                    if (is_numeric($message['Content'])) {
                        $action_number = intval($message['Content']);
                        // 数字事件
                        $info = WechatMessageHandler::getInstance()->popMessage($message['FromUserName']);
//                        LogHandler::log('wechat', 'debug', $info);
                        if ($info && isset($info['action'])) {
                            switch ($info['action']) {
                                case 'image':
                                    switch ($action_number) {
                                        /**
                                        [1]：通用文字识别
                                        [2]：通用文字识别（高精度版）
                                        [3]：网络图片文字识别
                                        [4]：身份证识别
                                        [5]：银行卡识别
                                        [6]：营业执照识别
                                        [7]：表格文字识别
                                        [8]：驾驶证识别
                                        [9]：行驶证识别
                                        [10]：车牌识别
                                        [11]：通用票据识别
                                        [12]：火车票识别
                                         */
                                        case 1:
                                            $res = BaiduAIPHandler::getInstance('ocr')->basicGeneralUrl($info['data']['PicUrl'], [
                                                'detect_direction' => true,
                                                'detect_language' => true
                                            ]);
                                            if (! isset($res['error_code']) && $res['words_result_num'] > 0) {
                                                $data = collect($res['words_result'])->pluck('words')->implode("\n");
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;
                                        case 2:
                                                $res = BaiduAIPHandler::getInstance('ocr')->basicAccurate(file_get_contents($info['data']['PicUrl']), [
                                                    'detect_direction' => true,
                                                ]);
                                            if (! isset($res['error_code']) && $res['words_result_num'] > 0) {
                                                $data = collect($res['words_result'])->pluck('words')->implode("\n");
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;
                                        case 3:
                                            $res = BaiduAIPHandler::getInstance('ocr')->webImageUrl($info['data']['PicUrl'], [
                                                'detect_direction' => true,
                                                'detect_language' => true
                                            ]);
                                            if (! isset($res['error_code']) && $res['words_result_num'] > 0) {
                                                $data = collect($res['words_result'])->pluck('words')->implode("\n");
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;
                                        case 4:
                                            $res = BaiduAIPHandler::getInstance('ocr')->idcard(file_get_contents($info['data']['PicUrl']), 'front', [
                                                'detect_direction' => true,
                                                'detect_language' => true
                                            ]);
                                            $data = '';
                                            if (! isset($res['error_code']) && $res['words_result_num'] > 0) {
                                                foreach ($res['words_result'] as $key => $value) {
                                                    $data .= "{$key}：{$value['words']}\n";
                                                }
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;
                                        case 5:
                                            $res = BaiduAIPHandler::getInstance('ocr')->bankcard(file_get_contents($info['data']['PicUrl']));
                                            $data = '';
                                            if (! isset($res['error_code']) && $res['result']) {
                                                $cards = ['银行卡', '借记卡', '信用卡'];
                                                $data = $res['result']['bank_name'] . $cards[$res['result']['bank_card_type']] . '：' . $res['result']['bank_card_number'];
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;

                                        case 6:
                                            $res = BaiduAIPHandler::getInstance('ocr')->businessLicense(file_get_contents($info['data']['PicUrl']));
                                            $data = '';
                                            if (! isset($res['error_code']) &&  $res['words_result_num'] > 0) {
                                                foreach ($res['words_result'] as $key => $value) {
                                                    $data .= "{$key}：{$value['words']}\n";
                                                }
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;
                                        case 7:
                                            $res = BaiduAIPHandler::getInstance('ocr')->drivingLicense(file_get_contents($info['data']['PicUrl']), [
                                                'detect_direction' => true,
                                            ]);
                                            $data = '';
                                            if (! isset($res['error_code']) && $res['words_result_num'] > 0) {
                                                foreach ($res['words_result'] as $key => $value) {
                                                    $data .= "{$key}：{$value['words']}\n";
                                                }
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                        case 8:
                                            $res = BaiduAIPHandler::getInstance('ocr')->vehicleLicense(file_get_contents($info['data']['PicUrl']), [
                                                'detect_direction' => true,
                                                'accuracy' => 'normal'
                                            ]);
                                            $data = '';
                                            if (! isset($res['error_code']) && $res['words_result_num'] > 0) {
                                                foreach ($res['words_result'] as $key => $value) {
                                                    $data .= "{$key}：{$value['words']}\n";
                                                }
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;

                                        case 9:
                                            $res = BaiduAIPHandler::getInstance('ocr')->licensePlate(file_get_contents($info['data']['PicUrl']));
                                            $data = '';
                                            if (! isset($res['error_code']) && $res['words_result']) {
                                                $data = $res['words_result']['number'];
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;
                                        case 10:
                                            $res = BaiduAIPHandler::getInstance('ocr')->receipt(file_get_contents($info['data']['PicUrl']), [
                                                'recognize_granularity' => 'big',
                                                'probability' => false,
                                                'accuracy' => 'normal',
                                                'detect_direction' => true,
                                            ]);
                                            $data = '';
                                            if (! isset($res['error_code']) && $res['words_result_num'] > 0) {
                                                $data = collect($res['words_result'])->pluck('words')->implode("\n");
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;
                                        case 11:
                                            $res = BaiduAIPHandler::getInstance('ocr')->trainTicket(file_get_contents($info['data']['PicUrl']));
                                            $data = '';
                                            if (! isset($res['error_code'])) {
                                                $data[] = "乘车人：{$res['name']}";
                                                $data[] = "车号：{$res['train_num']}";
                                                $data[] = "车票号：{$res['ticket_num']}";
                                                $data[] = "车票类型：{$res['seat_category']}";
                                                $data[] = "票价：{$res['ticket_rates']}";
                                                $data[] = "发车日期：{$res['date']}";
                                                $data[] = "始发站：{$res['starting_station']}";
                                                $data[] = "终点站：{$res['destination_station']}";
                                            } else {
                                                $data = '识别失败，请稍后重试';
                                            }
                                            return $data;
                                            break;
                                    }
                                    break;
                                case 'text':
                                    switch ($action_number) {
                                        case 1:
                                            if (strlen($info['data']['Content']) > 511) {
                                                return '识别内容过长，推荐约250个中文汉字或500个英文字符';
                                            }
                                            $res = BaiduAIPHandler::getInstance('nlp')->ecnet($info['data']['Content']);
                                            if (! isset($res['error_code']) && $res['item'] > 0) {
                                                if ($res['item']['vec_fragment']) {
                                                    $error = '';
                                                    foreach ($res['item']['vec_fragment'] as $item) {
                                                        $error .= "{$item['ori_frag']} => {$item['correct_frag']}";
                                                    }
                                                    $error .= "修正后：".$res['item']['correct_query'];
                                                    return $error;
                                                } else {
                                                    return "无错误，很完美～";
                                                }
                                            } else {
                                                $data = '处理失败，请稍后重试';
                                            }
                                            return $data;
                                            break;

                                    }
                                    break;
                            }
                        }
                    } else {
                        WechatMessageHandler::getInstance()->pushAction('text', $message);
                        return WechatMessageHandler::getInstance()->getMenu('text');
                    }
                    return '收到文字消息';
                    break;
                case 'image':
                    //{"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543482762","MsgType":"image","PicUrl":"http:\/\/mmbiz.qpic.cn\/mmbiz_jpg\/FWEnHfswJqibTSA3zVGVgPkgPrvxvGbCkSiaOVC61V6ia7MT2EJ8WRP8CrYLiaBv0d3wbN0DMcxziaBvVwNCiadCOMOQ\/0","MsgId":"6629207985152081392","MediaId":"_le8aXg-eDTiKVQiWZEiU8442bSJekpm_K46Z4MjzlsvqMRz-5MxozTIlyzztUej"}
                    dispatch(new ReceiveWechatMessage($message));
                    WechatMessageHandler::getInstance()->pushAction('image', $message);
                    return WechatMessageHandler::getInstance()->getMenu('image');
                    return '收到图片消息';
                    break;
                case 'voice':
                    //{"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543482833","MsgType":"voice","MediaId":"uoRbjmGcxRUZ2jH_WIUdFX1m_gtl92aoZmjrJdldBOHMDffW3OQH6T-LjI462oha","Format":"amr","MsgId":"6629208290094759410","Recognition":null}
                    dispatch(new ReceiveWechatMessage($message));
                    return '收到语音消息';
                    break;
                case 'video':
                    //{"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543483012","MsgType":"video","MediaId":"IoQz8qi9D4CUyMcvBrIkzRSSHd7-F-ujFQXu4YpaDH0WOASXHghafuVRcj4q-MJ5","ThumbMediaId":"H39obNMBH9tHuufYuMnlB_AeeX-cOr4pSwAl4njgqXDgChR7BWNPbj8ndfW9sq2t","MsgId":"6629209058893905397"}
                    dispatch(new ReceiveWechatMessage($message));
                    return '收到视频消息';
                    break;
                case 'location':
                    //{"ToUserName":"gh_f00d12a6807f","FromUserName":"o3l5Twydfo3yGJMLtqCDZdmVKkW8","CreateTime":"1543483021","MsgType":"location","Location_X":"22.962814","Location_Y":"113.892606","Scale":"15","Label":"东莞市肯德基汽车穿梭餐厅(>松山湖DT店)","MsgId":"6629209097548611063"}
                    dispatch(new ReceiveWechatMessage($message));
                    return '收到坐标消息';
                    break;

                default:
                    return '收到其它消息';
                    break;
            }
        });

        return $app->server->serve();
    }
}