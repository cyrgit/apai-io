<?php

namespace ApaiIO\ResponseTransformer;

class ObjectToResult extends ObjectToArray implements ResponseTransformerInterface {

    public function transform($response)
    {
        $data = array();
        $response = $this->buildArray( $response );

        if( !isset( $response['Items']['Item'] ) )
        {
            return $data;
        }

        foreach( $response['Items']['Item'] as $item )
        {
            if( !isset( $item['ItemAttributes']['Title'] ) )
            {
                continue;
            }

            /**
             * Filter for Prime-Only items by ensuring they are available for
             * Super Saver Shipping
             */
            if( !isset( $item['Offers']['Offer']['OfferListing']['IsEligibleForSuperSaverShipping'] )
                OR $item['Offers']['Offer']['OfferListing']['IsEligibleForSuperSaverShipping'] != 1 )
            {
                continue;
            }

            $row = array();

            $row['asin'] = $item['ASIN'];
            $row['title'] = strip_tags( $item['ItemAttributes']['Title'] );

            if( isset( $item['LargeImage']['URL'] ) )
            {
                $row['large_image'] = $item['LargeImage']['URL'];
            }

            if( isset( $item['MediumImage']['URL'] ) )
            {
                $row['medium_image'] = $item['MediumImage']['URL'];
            }

            if( isset( $item['SmallImage']['URL'] ) )
            {
                $row['small_image'] = $item['SmallImage']['URL'];
            }

            if( isset( $item['ItemAttributes']['ISBN'] ) )
            {
                $row['isbn'] = $item['ItemAttributes']['ISBN'];
            }

            if( isset( $item['ItemAttributes']['Edition'] ) )
            {
                $row['edition'] = $item['ItemAttributes']['Edition'];
            }

            if( isset( $item['ItemAttributes']['Author'] ) )
            {
                $author = $item['ItemAttributes']['Author'];
                if( ! is_array( $author ) )
                {
                    $author = array($author);
                }
                $row['author'] = $author;
            }

            $data[] = $row;
        }

        return $data;
    }

}
