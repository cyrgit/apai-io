<?php
namespace ApaiIO\ResponseTransformer;

class ObjectToPreview extends ObjectToArray implements ResponseTransformerInterface
{
    /**
     *
     * @var array
     */
    protected $data = [];

    /**
     *
     * @var array
     */
    protected $items = [];

    /**
     *
     * @var array
     */
    protected $categories = [];

    /**
     *
     * @param string $response
     * @return array
     */
    public function transform($response)
    {
        $response = parent::transform($response);

        if (!$this->getItems($response)) {
            return [];
        }

        $this->getCategories($response);

        $c = count($this->items);
        for ($i = 0; $i < $c; $i++) {
            $this->set($i, 'asin', 'ASIN');
            if (!$this->set($i, 'category', 'BrowseNodes', 'BrowseNode', 'Name')) {
                $this->set($i, 'category', 'BrowseNodes', 'BrowseNode', 0, 'Name');
            }
            $this->set($i, 'sales_rank', 'SalesRank');
            $this->set($i, 'title', 'ItemAttributes', 'Title');
            $this->set($i, 'url', 'DetailPageURL');
            $this->set($i, 'manufacturer', 'ItemAttributes', 'Manufacturer');
            $this->set($i, 'large_image', 'LargeImage', 'URL');
            $this->set($i, 'medium_image', 'MediumImage', 'URL');
            $this->set($i, 'small_image', 'SmallImage', 'URL');
        }

        return [
            'items' => $this->data,
            'categories' => $this->categories
        ];
    }

    /**
     *
     * @param type $response
     * @return mixed
     */
    protected function getItems($response)
    {
        if (isset($response['Items']['Item']) AND is_array($response['Items']['Item'])) {
            return $this->items = $response['Items']['Item'];
        } else {
            return false;
        }
    }

    /**
     *
     * @param array $response
     */
    protected function getCategories($response)
    {
        if (isset($response['Items']['SearchBinSets']['SearchBinSet']['Bin'])) {
            foreach ($response['Items']['SearchBinSets']['SearchBinSet']['Bin'] as $bin) {
                if (isset($bin['BinParameter']['Name']) && $bin['BinParameter']['Name'] == 'SearchIndex') {
                    $this->categories[] = $bin['BinParameter']['Value'];
                }
                if (count($this->categories) >= 10) {
                    return;
                }
            }
        }
    }

    /**
     *
     * @param int $i
     * @param string $data
     * @param string $key1
     * @param string $key2
     * @param string $key3
     * @param string $key4
     */
    protected function set($i, $data, $key1, $key2 = null, $key3 = null, $key4 = null)
    {
        if ($key4 !== null) {
            if (isset($this->items[$i][$key1][$key2][$key3][$key4])) {
                $this->data[$i][$data] = $this->items[$i][$key1][$key2][$key3][$key4];
                return true;
            }
        } elseif ($key3 !== null) {
            if (isset($this->items[$i][$key1][$key2][$key3])) {
                $this->data[$i][$data] = $this->items[$i][$key1][$key2][$key3];
                return true;
            }
        } elseif ($key2 !== null) {
            if (isset($this->items[$i][$key1][$key2])) {
                $this->data[$i][$data] = $this->items[$i][$key1][$key2];
                return true;
            }
        } else {
            if (isset($this->items[$i][$key1])) {
                $this->data[$i][$data] = $this->items[$i][$key1];
                return true;
            }
        }
        return false;
    }
}
