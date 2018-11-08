<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 6.イテレーター
 *「反復」という意味
 * 集合オブジェクト（ひとつのオブジェクトに複数の要素が含まれる、配列等）をループ操作する際、
 * イテレータークラスから複数の処理クラスに分岐し操作を分ける
 * これによりクライアント側からは処理を秘匿することができ、
 * また配列操作処理も後から付け加える形で追加修正することができる
 */

//個別処理のインターフェース
 interface AuthorIterator
 {
 	/**
 	 * 次の要素があるかどうかを判定する
 	 * @return bool
 	 */
 	public function hasNext();

 	/**
 	 * 次の要素を返し、ポインターを進める
 	 * @return mixed
 	 */
 	public function next();
 }

//イテレーターの処理の実装
class AuthorList_Simple_Iterator implements AuthorIterator
{
	private $authors;
	private $position = 0;

	function __construct( $authors )
    {
		$this->authors = $authors;
	}

	public function hasNext()
    {
		return isset( $this->authors[ $this->position ] );
	}

	public function next()
    {
		return $this->authors[ $this->position ++ ];
	}

}

// 別のIteratorクラス、処理内容が異なるが同じAuthorIteratorインターフェイスを持っており、
// クライアント側からは差異はない
class AuthorList_Detailed_Iterator implements AuthorIterator
{
	protected $authors;
	private $position = 0;

	function __construct( $authors )
    {
		$this->authors = $authors;
	}

	public function hasNext()
    {
		return isset( $this->authors[ $this->position ] );
	}

	public function next()
    {
		$author      = $this->authors[ $this->position ++ ];
		$family_name = $author['family-name'];
		$given_name  = $author['given-name'];

		return $family_name . ' - ' . $given_name;
	}
}

//各処理クラスにて共通のメソッドはtraitにて分離して使用する
trait AuthorList_Methods
{
	private $author_list;

	function __construct( $authors )
    {
		$this->author_list = $authors;
	}

	public function add_to_list( $author )
    {
		$this->author_list[] = $author;
	}

	public function get_author_list()
    {
		return $this->author_list;
	}
}

//著者を返す処理のインターフェース
interface AuthorList
{
   public function createIterator();
}

// 処理集約オブジェクト・著者一覧[簡易版]　(個別処理とtraitの処理を合わせる)
class Authors_Simple implements AuthorList
{
	use AuthorList_Methods;

	public function createIterator()
    {
		return new AuthorList_Simple_Iterator( $this->author_list );
	}
}

// 処理集約オブジェクト・著者一覧[詳細版]
class Authors_Detailed implements AuthorList
{
	use AuthorList_Methods;

	public function createIterator()
    {
		return new AuthorList_Detailed_Iterator( $this->author_list );
	}
}

//クライアントに用いるクラス
class Book
{
	private $author_iterator;

	function __construct( AuthorList $author_list )
    {
		// Iteratorオブジェクトを持たせる
		$this->author_iterator = $author_list->createIterator();
	}

	function print_authors()
    {
		// Iteratorオブジェクトを使う
		while ( $this->author_iterator->hasNext() )
        {
			$author = $this->author_iterator->next();
			echo $author . '<br>';
		}
	}
}

/*以下ビュー*/
// 著者一覧のデータ[簡易]
$authors_simple_array = [ 'Matumoto Jun', 'Kobayashi Kentaro', 'Mudata Shuichi', 'Murakami Ryu', 'Kamijou Touma' ];
// 著者一覧のデータ[詳細]
$authors_detailed_array = [
	[
		'family-name' => 'Matumoto',
		'given-name'  => 'Jun',
		'id'          => 5
	],
	[
		'family-name' => 'Kobayashi',
		'given-name'  => 'Kentaro',
		'id'          => 1,
	],
	[
		'family-name' => 'Mudata',
		'given-name'  => 'Shuichi',
		'id'          => 2
	],
	[
		'family-name' => 'Kamijou',
		'given-name'  => 'Touma',
		'id'          => 4
	],
	[
		'family-name' => 'Murakami',
		'given-name'  => 'Ryu',
		'id'          => 3
	],
];

// それぞれ違う集約オブジェクトで書籍クラスをインスタンス化
$book_a = new Book( new Authors_Simple( $authors_simple_array ) );
$book_b = new Book( new Authors_Detailed( $authors_detailed_array ) );

<?php $book_a->print_authors(); ?>

//Book->処理集約オブジェクト→個別処理オブジェクト・traitクラス
