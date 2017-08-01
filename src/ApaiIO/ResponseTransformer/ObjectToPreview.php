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
                    $this->categories[] = $this->categoryValue($bin['BinParameter']['Value']);
                }
                if (count($this->categories) >= 10) {
                    return;
                }
            }
        }
    }

    protected function categoryValue($value)
    {
        switch ($value) {
            case 'All':
                return ['key' => $value, 'value' => 'All Departments'];
                break;
            case 'ArtsAndCrafts':
                return ['key' => $value, 'value' => 'Arts, Crafts & Sewing'];
                break;
            case 'Collectibles':
                return ['key' => $value, 'value' => 'Collectibles & Fine Arts'];
                break;
            case 'Fashion':
                return ['key' => $value, 'value' => 'Clothing, Shoes & Jewelry'];
                break;
            case 'FashionBaby':
                return ['key' => $value, 'value' => 'Clothing, Shoes & Jewelry - Baby'];
                break;
            case 'FashionBoys':
                return ['key' => $value, 'value' => 'Clothing, Shoes & Jewelry - Boys'];
                break;
            case 'FashionGirls':
                return ['key' => $value, 'value' => 'Clothing, Shoes & Jewelry - Girls'];
                break;
            case 'FashionMen':
                return ['key' => $value, 'value' => 'Clothing, Shoes & Jewelry - Men'];
                break;
            case 'FashionWomen':
                return ['key' => $value, 'value' => 'Clothing, Shoes & Jewelry - Women'];
                break;
            case 'GiftCards':
                return ['key' => $value, 'value' => 'Gift Cards'];
                break;
            case 'Grocery':
                return ['key' => $value, 'value' => 'Grocery & Gourmet Food'];
                break;
            case 'HealthPersonalCare':
                return ['key' => $value, 'value' => 'Health & Personal Care'];
                break;
            case 'HomeGarden':
                return ['key' => $value, 'value' => 'Home & Kitchen'];
                break;
            case 'Industrial':
                return ['key' => $value, 'value' => 'Industrial & Scientific'];
                break;
            case 'KindleStore':
                return ['key' => $value, 'value' => 'Kindle Store'];
                break;
            case 'LawnAndGarden':
                return ['key' => $value, 'value' => 'Patio, Lawn & Garden'];
                break;
            case 'Luggage':
                return ['key' => $value, 'value' => 'Luggage & Travel Gear'];
                break;
            case 'Magazines':
                return ['key' => $value, 'value' => 'Magazine Subscriptions'];
                break;
            case 'MobileApps':
                return ['key' => $value, 'value' => 'Apps & Games'];
                break;
            case 'Movies':
                return ['key' => $value, 'value' => 'Movies & TV'];
                break;
            case 'MP3Downloads':
                return ['key' => $value, 'value' => 'Digital Music'];
                break;
            case 'Music':
                return ['key' => $value, 'value' => 'CDs & Vinyl'];
                break;
            case 'MusicalInstruments':
                return ['key' => $value, 'value' => 'Musical Instruments'];
                break;
            case 'OfficeProducts':
                return ['key' => $value, 'value' => 'Office Products'];
                break;
            case 'Pantry':
                return ['key' => $value, 'value' => 'Prime Pantry'];
                break;
            case 'PCHardware':
                return ['key' => $value, 'value' => 'Computers'];
                break;
            case 'PetSupplies':
                return ['key' => $value, 'value' => 'Pet Supplies'];
                break;
            case 'SportingGoods':
                return ['key' => $value, 'value' => 'Sports & Outdoors'];
                break;
            case 'Tools':
                return ['key' => $value, 'value' => 'Tools & Home Improvement'];
                break;
            case 'Toys':
                return ['key' => $value, 'value' => 'Toys & Games'];
                break;
            case 'UnboxVideo':
                return ['key' => $value, 'value' => 'Amazon Instant Video'];
                break;
            case 'VideoGames':
                return ['key' => $value, 'value' => 'Video Games'];
                break;
            case 'Wireless':
                return ['key' => $value, 'value' => 'Cell Phones & Accessories'];
                break;
            default:
                return ['key' => $value, 'value' => $value];
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
