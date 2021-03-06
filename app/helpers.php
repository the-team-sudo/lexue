<?php

/**
 * Shortcut for obtaining the app name
 *
 * @return mixed
 */
function appName()
{
    return config('app.name');
}

/**
 * Get the application domain or one of the sub-domains.
 * Root domain must be set from the env file
 *
 * @param null|string $prefix
 * @return null|string
 */
function appDomain($prefix = null)
{
    if (is_null($prefix)) {
        return config('app.domain');
    }

    return $prefix . '.' . config('app.domain');
}

/**
 * Get the sub-domain component
 * Inverse of appDomain()
 *
 * @return string|null
 */
function domainPrefix()
{
    $host = Request::getHost();

    if ($host === appDomain()) {
        return null;
    }

    $hostWithoutRootDomain = str_replace(appDomain(), '', $host);
    return $hostWithoutRootDomain = trim($hostWithoutRootDomain, '.');
}

/**
 * Whether the request url begins with "m."
 *
 * @return bool
 */
function isWechat()
{
    $isMobile = starts_with(Request::getHost(), 'm.');

    return $isMobile;
}

/**
 * Determine the user type from the request. Can be 'students', 'teachers' or 'admins'
 *
 * @return \Illuminate\Routing\Route|object|string
 */
function userType()
{
    return str_replace('m.', '', domainPrefix());
}

/**
 * Returns the localized translation of user type
 *
 * @return string|\Symfony\Component\Translation\TranslatorInterface
 */
function userTypeCn()
{
    return trans('user.type.' . userType());
}

/**
 * Returns the currently authenticated user by its type
 *
 * @return \Illuminate\Contracts\Auth\Authenticatable|null
 */
function authUser()
{
    return Auth::guard(userType())->user();
}

/**
 * Returns the id of the currently authenticated user by its type
 *
 * @return int|null
 */
function authId()
{
    return Auth::guard(userType())->id();
}

/**
 * Check if the current user type is logged in
 *
 * @return bool
 */
function authCheck()
{
    return Auth::guard(userType())->check();
}

/**
 * "backend" for teachers and admins
 * "wechat" for student views
 * "frontend" as a fallback for students
 *
 * @return string
 */
function viewPrefix()
{
    $prefix = "";
    $userType = userType();

    if ($userType === 'teachers' || $userType === 'admins') {
        $prefix = 'backend.';
    } elseif ($userType === 'students') {
        $prefix = isWechat() ? 'wechat.' : 'frontend.';
    }

    return $prefix;
}

/**
 * @param $route
 * @return bool
 */
function isPageActive($route)
{
    if ($bct = \Page::bct()) {
        $activeRoutes = $bct->pluck('route');
        return $activeRoutes->contains($route);
    }

    return false;
}

function humanDateTime($timestamp, $showDayOfWeek = true)
{
    $date = humanDate($timestamp, $showDayOfWeek);
    $time = humanTime($timestamp);
    return $date . ', ' . $time;
}

function humanTime($timestamp)
{
    $time = Carbon::parse($timestamp);
    return humanDayPart($time->hour) . $time->format('H:i');
}

function humanDate($timestamp, $showDayOfWeek = false)
{
    $date = Carbon::parse($timestamp)->format('m月j日');

    if ($showDayOfWeek) {
        $date .=  ' ' . humanDayOfWeek(Carbon::parse($timestamp)->dayOfWeek);
    }

    return $date;
}

function humanDayOfWeek($dayNumber)
{
    return trans('times.day_of_week.' . $dayNumber);
}

function humanDayPart($hour)
{
    if ($hour < 12) {
        return '上午';
    } elseif ($hour < 19) {
        return '下午';
    } else {
        return '晚上';
    }
}

/**
 * Generate absolute path to route file given the file name
 * All route files are stored in app\Http\Routes
 *
 * @param $file
 * @return string
 */
function routeFile($file)
{
    return app_path('Http' . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . $file);
}

/**
 * Pads nested arrays to be of equal length resursively
 *
 * @param array|\Illuminate\Support\Collection $array
 * @return array
 */
function padArray($array)
{
    $length = 0;

    /* first we get the max length */
    foreach ($array as $item) {
        if (! is_array($item) AND ! $item instanceof \Illuminate\Support\Collection) {
            continue;
        } elseif (count($item) > $length) {
            $length = count($item);
        }
    }

    if ($length === 0) {
        return $array;
    }

    /* now fill it up */
    foreach ($array as $item) {
        if (is_array($item) OR $item instanceof \Illuminate\Support\Collection) {
            fillArray($item, $length, '');
        }
    }

    return $array;
}

/**
 * @param array|\Illuminate\Support\Collection $array
 * @param int $length
 * @param null|mixed $filler
 * @return array
 */
function fillArray($array, int $length, $filler = null)
{
    if (count($array) < $length){
        $currentLength = count($array);
        for ($i = $currentLength; $i < $length; $i++) {
            $array[] = $filler;
        }
    }
}

/**
 * Generate random trade number.
 *
 * @return string
 */
function generateTradeNo()
{
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn = $yCode[intval(date('Y')) - 2015] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

    return $orderSn;
}

/**
 * Get the wechat subscribe page url.
 *
 * @return string
 */
function getSubscribeUrl()
{
    $url = 'http://mp.weixin.qq.com/s?__biz=MzIzMjI2NjAzMQ==&mid=2247483697&idx=1&sn=f0af35b6fa1f35ecb943d8e81d782549&chksm=e896c171dfe14867da9c9fde8148a568ddeb3d7f16500485bdff83d8179dfc0f2ce7c5bc1dcb#rd';
    return $url;
}