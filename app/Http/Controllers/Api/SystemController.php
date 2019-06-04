<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2018/10/17
 * Time: 22:51
 */

namespace App\Http\Controllers\Api;

use App\Handlers\AmapHandler;
use App\Models\FreightConfig;
use App\Models\PackageConfig;
use App\Transformers\FreightConfigTransformer;
use App\Transformers\PackageConfigTransformer;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function getConfig($key)
    {
        switch ($key) {
            case 'weapp_contact_information':
            default:
                $value = config($key, '');
                break;
        }
        return $this->response->array([
            'value' => $value
        ]);
    }

    public function weather(Request $request)
    {
        $Amap = AmapHandler::getInstance()->getAmap();
        if ($request->longitude && $request->latitude) {
            // 经纬度
            $mapInfo = $Amap->regeo($request->longitude . ',' . $request->latitude);
            if ($Amap->errCode) {
                $city = config('mall.weather_default_city');
            } else {
                if (!empty($mapInfo['regeocode']['addressComponent']['city'])) {
                    $city = $mapInfo['regeocode']['addressComponent']['city'];
                } else if (!empty($mapInfo['regeocode']['addressComponent']['province'])) {
                    $city = $mapInfo['regeocode']['addressComponent']['province'];
                } else {
                    $city = config('mall.weather_default_city');
                }
            }
        } else {
            $city = config('mall.weather_default_city');
        }

        $weatherInfo = $Amap->weather($city);
        if ($Amap->errCode || !isset($weatherInfo['lives'][0])) {
            return $this->response->noContent();
        } else {
            $weather = $weatherInfo['lives'][0];
            $weather['icon'] = $this->_getWeatherIcon($weather['weather']);
            return $this->response->array($weather);
        }
    }

    protected function _getWeatherIcon($weather = '')
    {
        switch ($weather) {
            case '晴':
            case '热':
                $icon = 'sunny';
                break;
            case '多云':
                $icon = 'cloudy';
                break;
            case '晴间多云':
                $icon = 'partly-cloudy';
                break;
            case '大部多云':
                $icon = 'mostly-cloudy';
                break;
            case '阴':
                $icon = 'overcast';
                break;
            case '阵雨':
            case '雷阵雨':
            case '小雨':
            case '中雨':
            case '冻雨':
                $icon = 'shower';
                break;
            case '雷阵雨伴有冰雹':
                $icon = 'thundershower-with-hail';
                break;
            case '大雨':
                $icon = 'heavy-rain';
                break;
            case '暴雨':
            case '大暴雨':
            case '特大暴雨':
                $icon = 'storm';
                break;
            case '雨夹雪':
                $icon = 'sleet';
                break;
            case '阵雪':
            case '小雪':
                $icon = 'snow-flurry';
                break;
            case '冷':
            case '中雪':
            case '大雪':
            case '暴雪':
                $icon = 'moderate-snow';
                break;
            case '浮尘':
            case '扬沙':
            case '沙尘暴':
            case '强沙尘暴':
                $icon = 'dust';
                break;
            case '雾':
                $icon = 'foggy';
                break;
            case '霾':
                $icon = 'haze';
                break;
            case '风':
            case '大风':
                $icon = 'Windy';
                break;
            case '飓风':
            case '热带风暴':
            case '龙卷风':
                $icon = 'Hurricane';
                break;
            default:
                $icon = 'unknown';
                break;
        }

        $file = 'weather/'.$icon.'.png';
        return \Storage::disk('template')->url($file);
    }

    // 获取通用海报列表
    public function getInviteTemplates()
    {
        $templates = config('mall.integral_templates', []);
        $return = array_map(function ($item) {
            return [
                'id' => $item['template_id'],
                'url' => \Storage::disk($item['template_disk'])->url($item['template_filename']),
            ];
        }, $templates);
        $return = array_values($return);

        return $this->response->array($return);
    }

    public function packageConfigs()
    {
        return $this->response->collection(PackageConfig::getConfigsByCache(\Auth::user()->area_id), new PackageConfigTransformer());
    }

    public function freightConfigs()
    {
        return $this->response->collection(FreightConfig::getConfigsByCache(\Auth::user()->area_id), new FreightConfigTransformer());
    }
}