<?php
/**
 * @author xialeistudio
 * @date 2019-05-17
 */

namespace tests\tasks;


class DemoTask
{
    public function demo($a, $b)
    {
        printf("a:%s b:%s\n", $a, $b);
        return 'ok';
    }
}