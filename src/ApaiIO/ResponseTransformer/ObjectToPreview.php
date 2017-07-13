<?php
/**
 *
 */
namespace ApaiIO\ResponseTransformer;

/**
 *
 */
class ObjectToPreview extends ObjectToArray implements ResponseTransformerInterface
{
	/**
	 *
	 * @var type
	 */
    protected $data = array();

	/**
	 *
	 * @var type
	 */
	protected $items = array();

	/**
	 *
	 * @param type $response
	 * @return type
	 */
	public function transform($response)
    {
        $response = parent::transform($response);

        if( ! $this->get_items( $response ) )
		{
			return array();
		}
        $c = count( $this->items );
        for( $i=0; $i < $c; $i++ )
        {
            $this->set( $i, 'asin', 'ASIN' );
            $this->set( $i, 'sales_rank', 'SalesRank' );
            $this->set( $i, 'title', 'ItemAttributes', 'Title' );
            $this->set( $i, 'url', 'DetailPageURL');
            $this->set( $i, 'manufacturer', 'ItemAttributes', 'Manufacturer' );
            $this->set( $i, 'large_image', 'LargeImage', 'URL' );
            $this->set( $i, 'medium_image', 'MediumImage', 'URL' );
            $this->set( $i, 'small_image', 'SmallImage', 'URL' );
            $this->get_price($i);
        }

        return $this->data;
    }

	/**
	 *
	 * @param type $response
	 * @return mixed
	 */
	protected function get_items($response)
	{
		if( isset($response['Items']['Item']) AND is_array($response['Items']['Item']) )
		{
			return $this->items = $response['Items']['Item'];
		}
		else
		{
			return FALSE;
		}
	}


	/**
	 *
	 * @param type $data
	 * @param type $key1
	 * @param type $key2
	 * @param type $key3
	 */
	protected function set($i, $data, $key1, $key2=NULL, $key3=NULL)
	{
		if($key3)
		{
			if( isset($this->items[$i][$key1][$key2][$key3]) )
			{
				$this->data[$i][$data] = $this->items[$i][$key1][$key2][$key3];
			}
		}
		elseif($key2)
		{
			if( isset($this->items[$i][$key1][$key2]) )
			{
				$this->data[$i][$data] = $this->items[$i][$key1][$key2];
			}
		}
		else
		{
			if( isset($this->items[$i][$key1]) )
			{
				$this->data[$i][$data] = $this->items[$i][$key1];
			}
		}
	}

    protected function set_array($i, $data, $key1, $key2=NULL, $key3=NULL)
    {
        $this->set($i, $data, $key1, $key2, $key3);
        if(isset($this->data[$i][$data]) AND !is_array( $this->data[$i][$data]))
        {
            $this->data[$i][$data] = array($this->data[$i][$data]);
        }
    }

    private function get_price($i)
    {
        $list_price = isset( $this->items[$i]['ItemAttributes']['ListPrice']['Amount'] ) ? $this->items[$i]['ItemAttributes']['ListPrice']['Amount'] : NULL;
        $amazon_price = isset( $this->items[$i]['Offers']['Offer']['OfferListing']['Price']['Amount'] ) ? $this->items[$i]['Offers']['Offer']['OfferListing']['Price']['Amount'] : NULL;
        $saved = isset( $this->items[$i]['Offers']['Offer']['OfferListing']['AmountSaved'] ) ? $this->items[$i]['Offers']['Offer']['OfferListing']['AmountSaved']['Amount'] : NULL;
        $price = ($list_price) ? $list_price : ($amazon_price ? ($amazon_price + $saved) : NULL );
        $this->data[$i]['price'] = ($price) ? $price : (isset($this->data[$i]['lowest_new_price']) ? $this->data[$i]['lowest_new_price'] : NULL);
    }
}